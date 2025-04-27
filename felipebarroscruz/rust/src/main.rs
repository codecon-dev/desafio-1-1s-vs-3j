use actix_multipart::Multipart;
use actix_web::body::MessageBody;
use actix_web::{web, App, HttpResponse, HttpServer};
use chrono::Utc;
use futures::stream::FuturesUnordered;
use futures::{StreamExt, TryStreamExt};
use itertools::Itertools;
use lazy_static::lazy_static;
use memoize::memoize;
use parking_lot::RwLock;
use serde::{Deserialize, Serialize};
use std::collections::HashMap;
use std::sync::Arc;
use std::time::Instant;

const DEFAULT_FILE_NAME: &str = "file";

#[derive(Debug, Serialize, Deserialize, Clone)]
struct User {
    id: String,
    nome: String,
    idade: i32,
    score: i32,
    ativo: bool,
    pais: String,
    equipe: Team,
    logs: Vec<Log>,
}

#[derive(Debug, Serialize, Deserialize, Clone)]
struct Team {
    nome: String,
    lider: bool,
    projetos: Vec<Project>,
}

#[derive(Debug, Serialize, Deserialize, Clone)]
struct Project {
    nome: String,
    concluido: bool,
}

#[derive(Debug, Serialize, Deserialize, Clone)]
struct Log {
    data: String,
    acao: String,
}

#[derive(Debug, Serialize, Deserialize)]
struct ApiResponse<T> {
    status: i32,
    timestamp: String,
    execution_time_ms: String,
    data: T,
}

#[derive(Debug, Serialize, Deserialize)]
struct ApiResponseError<T> {
    status: i32,
    timestamp: String,
    execution_time_ms: String,
    error: T,
}

#[derive(Debug, Serialize, Deserialize, Clone)]
struct CountryCount {
    country: String,
    count: usize,
}

#[derive(Debug, Serialize, Deserialize, Clone)]
struct TeamInsight {
    team: String,
    total_members: usize,
    active_members: usize,
    leaders: usize,
    completed_projects: HashMap<String, usize>,
    active_percentage: f64,
}

#[derive(Debug, Serialize, Deserialize, Clone)]
struct DailyActiveUsers {
    date: String,
    count: usize,
}

#[derive(Serialize)]
struct HealthResponse {
    status: i32,
    message: String,
}

type HandlerFn = Arc<dyn Fn() -> HttpResponse + Send + Sync>;

lazy_static! {
    static ref STORAGE: Arc<RwLock<HashMap<String, Vec<User>>>> =
        Arc::new(RwLock::new(HashMap::new()));
}

lazy_static! {
    static ref CACHE: Arc<RwLock<HashMap<String, Vec<u8>>>> = Arc::new(RwLock::new(HashMap::new()));
}

fn invalidate_cache() {
    CACHE.write().clear();
}

fn get_cached_value<T>(cache_key: &str) -> Option<T>
where
    T: for<'de> Deserialize<'de>,
{
    CACHE
        .read()
        .get(cache_key)
        .and_then(|cached_data| serde_json::from_slice(cached_data).ok())
}

fn set_cached_value<T>(cache_key: &str, value: &T) -> Result<(), String>
where
    T: Serialize,
{
    let serialized = serde_json::to_vec(value).map_err(|e| e.to_string())?;
    CACHE.write().insert(cache_key.to_string(), serialized);
    Ok(())
}

fn memoize<'a, T, F>(cache_key: &'a str, f: F) -> impl Fn() -> Result<Vec<T>, String> + 'a
where
    T: for<'de> Deserialize<'de> + Serialize + Clone,
    F: Fn() -> Result<Vec<T>, String> + 'a,
{
    move || {
        if let Some(result) = get_cached_value(cache_key) {
            return Ok(result);
        }

        let result = f()?;
        set_cached_value(cache_key, &result)?;
        Ok(result)
    }
}

fn memoize_fn<'a, T, F>(f: F) -> impl Fn() -> Result<Vec<T>, String> + 'a
where
    T: for<'de> Deserialize<'de> + Serialize + Clone,
    F: Fn() -> Result<Vec<T>, String> + 'a + std::any::Any,
{
    let cache_key = std::any::type_name_of_val(&f);
    memoize(cache_key, f)
}

#[memoize(SharedCache)]
fn get_users() -> Vec<User> {
    STORAGE.read().get("users").cloned().unwrap_or_default()
}

#[memoize(SharedCache)]
fn get_superusers() -> Result<Vec<User>, String> {
    Ok(get_users()
        .into_iter()
        .filter(|user| user.score >= 900 && user.ativo)
        .collect())
}

#[memoize(SharedCache)]
fn get_top_countries() -> Result<Vec<CountryCount>, String> {
    Ok(get_superusers()?
        .iter()
        .map(|user| &user.pais)
        .counts()
        .into_iter()
        .map(|(country, count)| CountryCount {
            country: country.to_string(),
            count,
        })
        .sorted_by(|a, b| b.count.cmp(&a.count))
        .take(5)
        .collect())
}

#[memoize(SharedCache)]
fn get_team_insights() -> Result<Vec<TeamInsight>, String> {
    let users = get_users();

    let mut team_groups: HashMap<String, Vec<&User>> = HashMap::new();
    for user in users.iter() {
        team_groups
            .entry(user.equipe.nome.clone())
            .or_default()
            .push(user);
    }

    Ok(team_groups
        .into_iter()
        .map(|(team_name, team_users)| {
            let total_members = team_users.len();
            let active_members = team_users.iter().filter(|u| u.ativo).count();
            let leaders = team_users.iter().filter(|u| u.equipe.lider).count();

            let active_percentage: f64 = format!(
                "{:.2}",
                ((active_members as f64 / total_members as f64) * 100.0)
            )
            .parse()
            .unwrap();

            TeamInsight {
                team: team_name,
                total_members,
                active_members,
                leaders,
                completed_projects: HashMap::new(),
                active_percentage,
            }
        })
        .collect())
}

#[memoize(SharedCache)]
fn get_active_users_per_day() -> Result<Vec<DailyActiveUsers>, String> {
    Ok(get_users()
        .iter()
        .flat_map(|user| &user.logs)
        .filter(|log| log.acao == "login")
        .map(|log| &log.data)
        .counts()
        .into_iter()
        .map(|(date, count)| DailyActiveUsers {
            date: date.to_string(),
            count,
        })
        .sorted_by(|a, b| b.date.cmp(&a.date))
        .collect())
}

fn calculate_execution_time(start: Instant) -> f64 {
    let execution_time = start.elapsed().as_nanos() as f64 / 1_000_000_000.0;
    execution_time
}

fn format_time(time: f64) -> String {
    format!("{}", time)
        .trim_end_matches('0')
        .trim_end_matches('.')
        .to_string()
}

fn get_timings(start: Instant) -> (String, String) {
    let timestamp = Utc::now().to_rfc3339();

    let execution_time_ms = format_time(calculate_execution_time(start));

    (timestamp, execution_time_ms)
}

fn match_result<T>(
    result: Result<T, String>,
    status: i32,
    timestamp: String,
    execution_time_ms: String,
) -> HttpResponse
where
    T: Serialize,
{
    let response_json = match result {
        Ok(data) => serde_json::json!(ApiResponse {
            status: status,
            timestamp: timestamp,
            execution_time_ms: execution_time_ms,
            data: data
        }),
        Err(error) => serde_json::json!(ApiResponseError {
            status: status,
            timestamp: timestamp,
            execution_time_ms: execution_time_ms,
            error: error
        }),
    };

    match status {
        201 => HttpResponse::Created().json(response_json),
        _ => HttpResponse::Ok().json(response_json),
    }
}

fn with_timing<T, F>(f: F, status: i32) -> HttpResponse
where
    T: Serialize,
    F: FnOnce() -> Result<T, String>,
{
    let start = Instant::now();
    let result = f();
    let (timestamp, execution_time_ms) = get_timings(start);

    match_result(
        result,
        status,
        timestamp.to_string(),
        execution_time_ms.to_string(),
    )
}

async fn with_async_timing<T, F, Fut>(f: F, status: i32) -> HttpResponse
where
    T: Serialize,
    F: FnOnce() -> Fut,
    Fut: std::future::Future<Output = Result<T, String>>,
{
    let start = Instant::now();
    let result = f().await;
    let (timestamp, execution_time_ms) = get_timings(start);

    match_result(
        result,
        status,
        timestamp.to_string(),
        execution_time_ms.to_string(),
    )
}

async fn upload_users_core(
    mut payload: Multipart,
    file_name: String,
) -> Result<serde_json::Value, String> {
    let mut users = Vec::new();
    let mut file_found = false;

    while let Ok(Some(mut field)) = payload.try_next().await {
        let content_disposition = field.content_disposition();

        if let Some(name) = content_disposition.get_name() {
            if name == file_name {
                file_found = true;

                let mut bytes = Vec::new();
                while let Some(chunk) = field.next().await {
                    match chunk {
                        Ok(chunk) => {
                            bytes.extend_from_slice(&chunk);
                        }
                        Err(_) => {
                            return Err("Error reading file".to_string());
                        }
                    }
                }

                match serde_json::from_slice::<Vec<User>>(&bytes) {
                    Ok(parsed_users) => {
                        users = parsed_users;
                    }
                    Err(e) => {
                        return Err(format!("Error parsing JSON: {}", e));
                    }
                }
                break;
            }
        }
    }

    if !file_found {
        return Err("No file field found in the request".to_string());
    }

    STORAGE.write().insert("users".to_string(), users.clone());
    invalidate_cache();
    Ok(serde_json::json!({ "users_count": users.len() }))
}

async fn post_users_handler(payload: Multipart) -> HttpResponse {
    let file_name: String =
        std::env::var("FILE_NAME").unwrap_or_else(|_| DEFAULT_FILE_NAME.to_string());
    let result = upload_users_core(payload, file_name).await;
    with_async_timing(|| async { result }, 201).await
}

fn get_superusers_handler() -> HttpResponse {
    with_timing(|| Arc::new(memoize_fn(get_top_countries))(), 200)
}

fn get_top_countries_handler() -> HttpResponse {
    with_timing(|| Arc::new(memoize_fn(get_top_countries))(), 200)
}

fn get_team_insights_handler() -> HttpResponse {
    with_timing(|| Arc::new(memoize_fn(get_team_insights))(), 200)
}

fn get_active_users_per_day_handler() -> HttpResponse {
    with_timing(|| Arc::new(memoize_fn(get_active_users_per_day))(), 200)
}

fn get_endpoint_handlers() -> HashMap<&'static str, HandlerFn> {
    let mut handlers = HashMap::new();
    handlers.insert("superusers", Arc::new(get_superusers_handler) as HandlerFn);
    handlers.insert(
        "top-countries",
        Arc::new(get_top_countries_handler) as HandlerFn,
    );
    handlers.insert(
        "team-insights",
        Arc::new(get_team_insights_handler) as HandlerFn,
    );
    handlers.insert(
        "active-users-per-day",
        Arc::new(get_active_users_per_day_handler) as HandlerFn,
    );
    handlers
}

async fn get_evaluation_handler() -> HttpResponse {
    let status = 200;
    let start = Instant::now();
    let mut results = HashMap::new();

    let handlers = get_endpoint_handlers();
    let mut futures = FuturesUnordered::new();

    for (endpoint, handler) in &handlers {
        let endpoint = endpoint.to_string();
        let handler = Arc::clone(handler);

        let future = async move {
            let handler_start = Instant::now();

            let result = handler();

            let bytes = result.into_body().try_into_bytes().unwrap_or_default();
            let is_valid_json = serde_json::from_slice::<serde_json::Value>(&bytes).is_ok();
            let data = serde_json::json!({
                "is_valid_json": is_valid_json
            });

            let (timestamp, execution_time_ms) = get_timings(handler_start);

            (endpoint, timestamp, execution_time_ms, data)
        };

        futures.push(future);
    }

    while let Some((endpoint, timestamp, execution_time_ms, data)) = futures.next().await {
        results.insert(
            endpoint,
            ApiResponse {
                status,
                timestamp,
                execution_time_ms,
                data,
            },
        );
    }

    let (timestamp, execution_time_ms) = get_timings(start);

    match_result(
        Ok(results),
        status,
        timestamp.to_string(),
        execution_time_ms.to_string(),
    )
}

async fn get_health_check_handler() -> HttpResponse {
    HttpResponse::Ok().json(HealthResponse {
        status: 200,
        message: "OK".to_string(),
    })
}

#[actix_web::main]
async fn main() -> std::io::Result<()> {
    let port = std::env::var("PORT").unwrap_or_else(|_| "8011".to_string());

    println!("Server running on port {}", port);

    HttpServer::new(|| {
        App::new()
            .route("/", web::get().to(get_health_check_handler))
            .route("/users", web::post().to(post_users_handler))
            .route(
                "/superusers",
                web::get().to(|| async { get_superusers_handler() }),
            )
            .route(
                "/top-countries",
                web::get().to(|| async { get_top_countries_handler() }),
            )
            .route(
                "/team-insights",
                web::get().to(|| async { get_team_insights_handler() }),
            )
            .route(
                "/active-users-per-day",
                web::get().to(|| async { get_active_users_per_day_handler() }),
            )
            .route("/evaluation", web::get().to(get_evaluation_handler))
    })
    .bind(format!("0.0.0.0:{}", port))?
    .run()
    .await
}

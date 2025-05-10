package services

import (
	"desafio-senior-vs-junior/models"
	"desafio-senior-vs-junior/repository"
	"runtime"
	"slices"
	"strings"
	"sync"
	"time"
)

type AnaliseService struct {
	repository *repository.UsuarioRepository
}

func NewAnaliseService(r *repository.UsuarioRepository) *AnaliseService {
	return &AnaliseService{r}
}

// GET /superusers
// Filtro: score >= 900 e active = true
// Retorna os dados e o tempo de processamento da requisição.
func (a *AnaliseService) SuperUsuarios() []models.User {
	users := a.repository.FindAll().Parallel(runtime.NumCPU()).Filter(func(u models.User) bool {
		return u.Score >= 900 && u.Ativo
	})
	return users.Collect()
}

type TopCountryResponse struct {
	Country string `json:"country"`
	Total   int    `json:"total"`
}

func groupBy[T any, K comparable](data []T, predicate func(item T) K) map[K][]T {
	result := make(map[K][]T)
	for _, item := range data {
		key := predicate(item)
		result[key] = append(result[key], item)
	}
	return result
}

// GET /top-countries
// Agrupa os superusuários por país.
// Retorna os 5 países com maior número de superusuários.
func (a *AnaliseService) TopPaises() []TopCountryResponse {
	superUsers := a.SuperUsuarios()
	countByCountry := make(map[string]int)

	for _, user := range superUsers {
		countByCountry[user.Pais]++
	}

	topCountries := make([]TopCountryResponse, 0, len(countByCountry))

	for country, count := range countByCountry {
		topCountries = append(topCountries, TopCountryResponse{
			Country: country,
			Total:   count,
		})
	}

	slices.SortStableFunc(topCountries, func(a, b TopCountryResponse) int {
		return b.Total - a.Total
	})

	if len(topCountries) > 5 {
		return topCountries[:5]
	}
	return topCountries
}

// GET /team-insights
// Agrupa por team.name.
// Retorna: total de membros, líderes, projetos concluídos e % de membros ativos.
func (a *AnaliseService) InsightEquipes() []models.Insight {
	users := a.repository.FindAll().Parallel(runtime.NumCPU()).Collect()
	usersGrouped := groupBy(users, func(u models.User) string {
		return u.Equipe.Nome
	})

	var wg sync.WaitGroup
	statsChan := make(chan models.Insight, len(usersGrouped))

	insights := make([]models.Insight, 0, len(usersGrouped))

	for nomeTime, usuarios := range usersGrouped {
		wg.Add(1)
		go func(nome string, membros []models.User) {
			defer wg.Done()

			stats := processEquipeStats(nome, membros)
			statsChan <- stats
		}(nomeTime, usuarios)
	}
	go func() {
		wg.Wait()
		close(statsChan)
	}()

	for stat := range statsChan {
		insights = append(insights, stat)
	}
	return insights
}

func processEquipeStats(nome string, membros []models.User) models.Insight {
	stats := models.Insight{
		Equipe:       nome,
		TotalMembros: len(membros),
	}
	ativos := 0
	for _, usuario := range membros {
		if usuario.Equipe.Lide {
			stats.Lideres++
		}
		if usuario.Ativo {
			ativos++
		}

		for _, projeto := range usuario.Equipe.Projetos {
			if projeto.Concluido {
				stats.ProjetosConcluidos++
			}
		}
	}
	pct := 0.0
	if stats.TotalMembros != 0 {
		pct = float64(ativos*100) / float64(stats.TotalMembros)
	}
	stats.PorcentualAtivos = pct
	return stats
}

type LoginPorDiaResponse struct {
	Data  time.Time `json:"data"`
	Total int       `json:"total"`
}

// GET /active-users-per-day
// Conta quantos logins aconteceram por data.
// Query param opcional: ?min=3000 para filtrar dias com pelo menos 3.000 logins.
func (a *AnaliseService) LoginPorDia(min int) []LoginPorDiaResponse {
	users := a.repository.FindAll().Collect()
	loginsByDayGrouped := sync.Map{}
	var wg sync.WaitGroup
	wokers := runtime.NumCPU()
	usersChan := make(chan models.User, len(users))
	for range wokers {
		wg.Add(1)
		go func() {
			defer wg.Done()
			for user := range usersChan {
				for _, log := range user.Logs {
					if strings.EqualFold(log.Acao, "login") {
						actual, _ := loginsByDayGrouped.LoadOrStore(log.Data, 0)
						loginsByDayGrouped.Store(log.Data, actual.(int)+1)
					}
				}
			}
		}()
	}
	for _, user := range users {
		usersChan <- user
	}
	close(usersChan)
	wg.Wait()
	loginPorDiaResponse := make([]LoginPorDiaResponse, 0)
	loginsByDayGrouped.Range(func(key, value any) bool {
		total := value.(int)
		if min == 0 || total >= min {
			loginPorDiaResponse = append(loginPorDiaResponse, LoginPorDiaResponse{
				Data:  key.(time.Time),
				Total: total,
			})
		}
		return true
	})
	slices.SortFunc(loginPorDiaResponse, func(a, b LoginPorDiaResponse) int {
		return a.Data.Compare(b.Data)
	})
	return loginPorDiaResponse
}

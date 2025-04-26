import express from 'express';
import bodyParser from 'body-parser';
import multer from 'multer';
import _ from 'lodash';

const PORT = process.env.PORT ?? 8011;

const __STORAGE__ = {};
const getStorageForFile = (filename) => multer({ storage: multer.memoryStorage() }).single(filename);

const invalidateCache = () => {
    __STORAGE__['superusers'] = undefined;
    getSuperUsers?.cache?.clear();
    getTopCountries?.cache?.clear();
    getTeamInsights?.cache?.clear();
    getActiveUsersPerDay?.cache?.clear();
};

const app = express();
app.use(bodyParser.json());

_.isJSON = (value) => {
    try {
        if (_.isObject(value)) {
            return true;
        }

        JSON.parse(value);
        return true;
    } catch {
        return false;
    }
}

const getUsers = () => __STORAGE__['file'] ?? [];

const filterSuperUsers = (users) => (
    users.filter(({ score, ativo }) => (
        score >= 900 && ativo
    ))
);

const getSuperUsers = _.memoize(() => (
    __STORAGE__['superusers'] ?? (
        __STORAGE__['superusers'] = filterSuperUsers(getUsers())
    )
));

const getTopCountries = _.memoize(() => (
    _.chain(getSuperUsers())
        .map(({ pais }) => pais)
        .countBy()
        .map((count, country) => ({ country, count }))
        .orderBy('count', 'desc')
        .take(5)
        .value()
));

const getTeamInsights = _.memoize(() => (
    _.chain(getUsers())
        .groupBy('equipe.nome')
        .map((users, team) => {
            const total_members = users.length;
            const active_members = _.filter(users, 'ativo').length;
            const leaders = _.filter(users, ['equipe.lider', true]).length;
            const completed_projects = _.chain(users)
                .flatMap('projetos')
                .filter('concluido')
                .countBy('nome')
                .value();
            
            const active_percentage = parseFloat(((active_members / total_members) * 100).toFixed(2));

            return {
                team,
                total_members,
                active_members,
                leaders,
                completed_projects,
                active_percentage
            };
        })
        .value()
));

const getActiveUsersPerDay = _.memoize(() => (
    _.chain(getUsers())
        .flatMap('logs')
        .filter(({ acao }) => acao === 'login')
        .countBy('data')
        .map((count, date) => ({ date, count }))
        .orderBy('date', 'desc')
        .value()
));

const getTimings = (handler, status = 200) => {
    return async (req, res, next) => {
        let result;
        const start = new Date();
        try {
            result = await handler(req, res, next);
        } catch (error) {
            result = error;
        } finally {
            const end = new Date().getTime();
            const execution_time_ms = (end - start.getTime()) / 1000;
            const response = result instanceof Error ? { error: result.message } : { data: result };
            res.status(status).json(Object.assign({}, { status, timestamp: start.toISOString(), execution_time_ms}, response));
        }
    }
}

app.get('/', (_req, res) => {
    res.status(200).end('');
});

app.post('/users', getStorageForFile('file'), getTimings((req) => {
    const users = JSON.parse(req.file.buffer.toString());
    __STORAGE__['file'] = users;
    invalidateCache();
    return { users_count: users?.length ?? 0 };
}, 201));

const getApiSuperusers = getTimings(() => getSuperUsers());

const getApiTopCountries = getTimings(() => getTopCountries());

const getApiTeamInsights = getTimings(() => getTeamInsights());

const getApiActiveUsersPerDay = getTimings(() => getActiveUsersPerDay());

const __GET_APIS__ = {
    'superusers': getApiSuperusers,
    'top-countries': getApiTopCountries,
    'team-insights': getApiTeamInsights,
    'active-users-per-day': getApiActiveUsersPerDay,
}

for (const [path, handler] of Object.entries(__GET_APIS__)) {
    app.get('/'.concat(path), handler);
}

app.get('/evaluation', getTimings(async () => {
    const tested_endpoints = {};
    const mockRes = (path) => ({
        status() {
            return this;
        },
        json({
            status,
            timestamp,
            execution_time_ms,
            data,
        }) {
            tested_endpoints[`/${path}`] = {
                status,
                timestamp,
                execution_time_ms,
                valid_response: _.isJSON(data)
            }
        }
    });

    await Promise.all(
        Object.entries(__GET_APIS__).map(([path, handler]) => 
            handler(undefined, mockRes(path), undefined)
        )
    );

    return tested_endpoints;
}));

app.listen(PORT, () => console.info(`Server is running on port ${PORT}`));

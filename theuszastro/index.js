const express = require('express');

const { middleware } = require('./upload');

const app = express();

const responses = {
	'/superusers': [],
	'/top-countries': [],
	'/team-insights': [],
	'/active-users-per-day': [],
};

app.post('/users', middleware(parse));
app.get('/superusers', (_, res) => {
	const start = Date.now();

	res.json({
		data: responses['/superusers'],
		execution_time_ms: `${(start - Date.now()) / 1000} ms`,
	});
});
app.get('/top-countries', (_, res) => {
	const start = Date.now();

	res.json({
		countries: responses['/top-countries'],
		execution_time_ms: `${(start - Date.now()) / 1000} ms`,
	});
});
app.get('/team-insights', (_, res) => {
	const start = Date.now();

	res.json({
		teams: responses['/team-insights'],
		execution_time_ms: `${(start - Date.now()) / 1000} ms`,
	});
});
app.get('/active-users-per-day', (_, res) => {
	const start = Date.now();

	res.json({
		logins: responses['/active-users-per-day'],
		execution_time_ms: `${(start - Date.now()) / 1000} ms`,
	});
});

app.listen(3333);

function parse(data) {
	responses['/superusers'] = data.filter(c => c.score >= 900 && c.active);

	const teams = {};
	const countries = {};
	const logins = {};

	for (let { active, country, team, logs } of data) {
		countries[country] ? (countries[country] += 1) : (countries[country] = 1);

		if (teams[team.name]) {
			teams[team.name].total_members += 1;
			teams[team.name].completed_projects += team.projects.filter(c => c.completed).length;
			teams[team.name].active_percentage += active ? 1 : 0;
			teams[team.name].leaders += team.leader ? 1 : 0;
		} else {
			teams[team.name] = {
				team: team.name,
				total_members: 1,
				leaders: team.leader ? 1 : 0,
				completed_projects: team.projects.filter(c => c.completed).length,
				active_percentage: 1,
			};
		}

		for (let { date } of logs.filter(c => c.action == 'login')) {
			logins[date] ? (logins[date] += 1) : (logins[date] = 1);
		}
	}

	responses['/active-users-per-day'] = Object.entries(logins).reduce((acc, item) => {
		acc.push({ date: item[0], total: item[1] });

		return acc;
	}, []);

	responses['/team-insights'] = Object.values(teams).map(item => ({
		...item,
		active_percentage: ((item.active_percentage / item.total_members) * 100).toFixed(2) + '%',
	}));

	responses['/top-countries'] = Object.entries(countries)
		.reduce((acc, item) => {
			acc.push({ country: item[0], total: item[1] });

			return acc;
		}, [])
		.sort((a, b) => b.total - a.total)
		.slice(0, 5);
}

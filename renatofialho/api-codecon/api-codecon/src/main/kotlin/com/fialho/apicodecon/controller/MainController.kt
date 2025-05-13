package com.fialho.apicodecon.controller

import com.fialho.apicodecon.api.CountriesResponse
import com.fialho.apicodecon.api.CountryResponse
import com.fialho.apicodecon.api.LoginsResponse
import com.fialho.apicodecon.api.SuperUsersResponse
import com.fialho.apicodecon.api.TeamInsightsResponse
import com.fialho.apicodecon.service.UsersService
import org.springframework.util.StopWatch
import org.springframework.web.bind.annotation.GetMapping
import org.springframework.web.bind.annotation.PostMapping
import org.springframework.web.bind.annotation.RequestParam
import org.springframework.web.bind.annotation.RestController
import org.springframework.web.multipart.MultipartFile
import java.time.OffsetDateTime

@RestController

class MainController(private val usersService: UsersService) {
    @PostMapping("/users")
    fun loadUsers(@RequestParam file : MultipartFile) {
        usersService.loadUsers(file)
    }

    @GetMapping("/superusers")
    fun getSuperusers(): SuperUsersResponse {
        val watch = StopWatch()
        watch.start()
        val superUsers = usersService.getSuperUsers()
        watch.stop()

        return SuperUsersResponse(
            superUsers,
            OffsetDateTime.now(),
            watch.totalTimeMillis
        )
    }

    @GetMapping("/top-countries")
    fun getTopCountries(): CountriesResponse {
        val watch = StopWatch()
        watch.start()
        val countries = usersService.getTopCountries();
        watch.stop()

        return CountriesResponse(
            countries = countries.map {
                CountryResponse(it.key, it.value)
            }.toList(),
            OffsetDateTime.now(),
            watch.totalTimeMillis
        )
    }


    @GetMapping("/team-insights")
    fun getTeamInsights(): TeamInsightsResponse {
        val watch = StopWatch()
        watch.start()
        val teamInsights = usersService.getTeamInsights();
        watch.stop()

        return TeamInsightsResponse(
            teams = teamInsights,
            OffsetDateTime.now(),
            watch.totalTimeMillis
        )
    }

    @GetMapping("/active-users-per-day")
    fun getActiveUsersPerDay(@RequestParam(required = false) min : Int?): LoginsResponse {
        val watch = StopWatch()
        watch.start()
        val logins = min?.let { usersService.getActiveUsersPerDay().filter { login -> login.total > it } }
            ?: usersService.getActiveUsersPerDay()
        watch.stop()

        return LoginsResponse(
            logins = logins,
            OffsetDateTime.now(),
            watch.totalTimeMillis
        )
    }
}
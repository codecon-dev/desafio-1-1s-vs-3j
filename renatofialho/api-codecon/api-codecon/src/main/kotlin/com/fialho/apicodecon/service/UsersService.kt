package com.fialho.apicodecon.service

import com.fasterxml.jackson.databind.ObjectMapper
import com.fasterxml.jackson.datatype.jsr310.JavaTimeModule
import com.fasterxml.jackson.module.kotlin.registerKotlinModule
import com.fialho.apicodecon.api.LoginResponse
import com.fialho.apicodecon.api.TeamInsightResponse
import com.fialho.apicodecon.domain.User
import org.springframework.beans.factory.annotation.Value
import org.springframework.core.io.Resource
import org.springframework.stereotype.Service
import org.springframework.web.multipart.MultipartFile
import java.math.BigDecimal
import java.math.RoundingMode


@Service
class UsersService() {

    @Value("classpath:usuarios_1000.json")
    lateinit var resourceFile: Resource
    var users: MutableList<User> = mutableListOf()

    fun loadUsers(file: MultipartFile) {
        val objectMapper = ObjectMapper()
            .registerKotlinModule()
            .registerModule(JavaTimeModule())

        objectMapper.readValue(file.bytes, Array<User>::class.java).forEach {
            users.add(it)
        }
    }

    fun getSuperUsers(): List<User> {
        return users
            .filter { it.active }
            .filter { it.score > 900 }
            .toList()
    }

    fun getTopCountries(): Map<String, Int> {
        return users
            .groupingBy { it.country }
            .eachCount()
            .asIterable()
            .take(5)
            .sortedByDescending { it.value }
            .associate { it.toPair() }
    }

    fun getActiveUsersPerDay(): List<LoginResponse> {
        return users
            .flatMap { it.logs }
            .filter { it.action == "login" }
            .groupingBy { it.date }
            .eachCount()
            .map { LoginResponse(it.key, it.value) }
            .toList()
    }

    fun getTeamInsights(): List<TeamInsightResponse> {
        val activeUsers = users
            .filter { it.active }
            .groupingBy { it.team.name }
            .eachCount()

        val totalMembersCount = users
            .groupingBy { it.team.name }
            .eachCount()

        val leaders = users
            .filter { it.team.leader }
            .groupingBy { it.team.name }
            .eachCount()

        val completedProjects = mutableMapOf<String, Int>()
            .withDefault { 0 };

        for (user in users) {
            for (project in user.team.projects) {
                if (project.completed) {
                    completedProjects
                        .compute(user.team.name, { _, u -> u?.plus(1) ?: 1 })
                }
            }
        }

        return totalMembersCount.map {
            TeamInsightResponse(
                it.key,
                it.value,
                leaders.getOrDefault(it.key, 0),
                completedProjects.getOrDefault(it.key, 0),
                BigDecimal(activeUsers.getOrDefault(it.key, 0).toDouble() / it.value.toDouble() * 100).setScale(
                    1,
                    RoundingMode.DOWN
                )
            )
        }


    }

}
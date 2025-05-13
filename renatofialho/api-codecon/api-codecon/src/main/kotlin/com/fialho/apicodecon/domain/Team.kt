package com.fialho.apicodecon.domain

data class Team(
    val name: String,
    val leader: Boolean,
    val projects: List<Project>
)

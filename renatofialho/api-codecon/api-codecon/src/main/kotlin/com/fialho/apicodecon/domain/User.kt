package com.fialho.apicodecon.domain

import java.util.UUID

data class User(
    val id: UUID,
    val name: String,
    val age: String,
    val score: Int,
    val active: Boolean,
    val country: String,
    val team: Team,
    val logs: List<Log>
)

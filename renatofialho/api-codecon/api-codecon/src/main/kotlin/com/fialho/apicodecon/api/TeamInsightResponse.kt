package com.fialho.apicodecon.api

import java.math.BigDecimal

data class TeamInsightResponse(
    val team: String,
    val totalMembers : Int,
    val leaders : Int,
    val completedProjects : Int,
    val activePercentage : BigDecimal
)

package com.fialho.apicodecon.domain

import java.time.LocalDate

data class Log(
    val date: LocalDate,
    val action: String
)

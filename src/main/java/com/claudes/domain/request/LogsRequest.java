package com.claudes.domain.request;

import java.time.LocalDate;


public class LogsRequest {
    private LocalDate date;
    private ActionEnum action;

    public LogsRequest() {
    }

    public LogsRequest(LocalDate date, ActionEnum action) {
        this.date = date;
        this.action = action;
    }

    public LocalDate getDate() {
        return date;
    }

    public void setDate(LocalDate date) {
        this.date = date;
    }

    public ActionEnum getAction() {
        return action;
    }

    public void setAction(ActionEnum action) {
        this.action = action;
    }
}

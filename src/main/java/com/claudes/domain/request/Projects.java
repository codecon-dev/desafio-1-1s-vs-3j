package com.claudes.domain.request;

public class Projects {
    private String name;
    private Boolean completed;

    public Projects() {
    }

    public Projects(String name, Boolean completed) {
        this.name = name;
        this.completed = completed;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public Boolean getCompleted() {
        return completed;
    }

    public void setCompleted(Boolean completed) {
        this.completed = completed;
    }
}

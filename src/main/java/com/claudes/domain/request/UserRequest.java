package com.claudes.domain.request;

import java.util.List;
import java.util.UUID;


public class UserRequest {

    private UUID id;
    private String name;
    private Integer age;
    private Integer score;
    private Boolean active;
    private String country;
    private TeamRequest team;
    private List<LogsRequest> logs;

    public UserRequest() {
    }

    public UserRequest(UUID id, String name, Integer age, Integer score, Boolean active, String country, TeamRequest team, List<LogsRequest> logs) {
        this.id = id;
        this.name = name;
        this.age = age;
        this.score = score;
        this.active = active;
        this.country = country;
        this.team = team;
        this.logs = logs;
    }



    public UUID getId() {
        return id;
    }

    public void setId(UUID id) {
        this.id = id;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public Integer getAge() {
        return age;
    }

    public void setAge(Integer age) {
        this.age = age;
    }

    public Integer getScore() {
        return score;
    }

    public void setScore(Integer score) {
        this.score = score;
    }

    public Boolean getActive() {
        return active;
    }

    public void setActive(Boolean active) {
        this.active = active;
    }

    public String getCountry() {
        return country;
    }

    public void setCountry(String country) {
        this.country = country;
    }

    public TeamRequest getTeam() {
        return team;
    }

    public void setTeam(TeamRequest team) {
        this.team = team;
    }

    public List<LogsRequest> getLogs() {
        return logs;
    }

    public void setLogs(List<LogsRequest> logs) {
        this.logs = logs;
    }
}

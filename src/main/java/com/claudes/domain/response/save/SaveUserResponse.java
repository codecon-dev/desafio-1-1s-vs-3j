package com.claudes.domain.response.save;

public class SaveUserResponse {
    public SaveUserResponse() {
    }

    public SaveUserResponse(String message, int userCount) {
        this.message = message;
        this.userCount = userCount;
    }

    private String message;
    private int userCount;

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public int getUserCount() {
        return userCount;
    }

    public void setUserCount(int userCount) {
        this.userCount = userCount;
    }
}

package com.claudes.domain.response;

public class ResponseDefault {
    private String timestamp;
    private long execution_time_ms;

    public ResponseDefault() {
    }

    public ResponseDefault(String timestamp, int execution_time_ms) {
        this.timestamp = timestamp;
        this.execution_time_ms = execution_time_ms;
    }

    public String getTimestamp() {
        return timestamp;
    }

    public void setTimestamp(String timestamp) {
        this.timestamp = timestamp;
    }

    public long getExecution_time_ms() {
        return execution_time_ms;
    }

    public void setExecution_time_ms(long execution_time_ms) {
        this.execution_time_ms = execution_time_ms;
    }
}

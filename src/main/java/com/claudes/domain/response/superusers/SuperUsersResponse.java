package com.claudes.domain.response.superusers;

import com.claudes.domain.request.UserRequest;
import com.claudes.domain.response.ResponseDefault;

import java.util.List;

public class SuperUsersResponse extends ResponseDefault {

    private List<UserRequest> data;

    public SuperUsersResponse() {
    }

    public SuperUsersResponse(String timestamp, int execution_time_ms, List<UserRequest> data) {
        super(timestamp, execution_time_ms);
        this.data = data;
    }

    public List<UserRequest> getData() {
        return data;
    }

    public void setData(List<UserRequest> data) {
        this.data = data;
    }
}

package com.claudes.controller;

import com.claudes.domain.request.UserRequest;
import com.claudes.domain.response.save.SaveUserResponse;
import com.claudes.domain.response.superusers.SuperUsersResponse;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.RestController;

import java.util.ArrayList;
import java.util.List;

@RestController
public class UserControllerImpl implements IUserController {

    public static List<UserRequest> users = new ArrayList<>();

    @Override
    public ResponseEntity<SaveUserResponse> saveUser(List<UserRequest> request) {
        var response = new SaveUserResponse("Arquivo recebido com sucesso.", request.size());
        users.addAll(request);

        return ResponseEntity.ok(response);
    }

    @Override
    public ResponseEntity<List<UserRequest>> getAllUsers() {
        return ResponseEntity.ok(users);
    }

    @Override
    public ResponseEntity<List<SuperUsersResponse>> getSuperUsers() {
        return null;
    }
}

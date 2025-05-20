package com.claudes.controller;

import com.claudes.domain.request.UserRequest;
import com.claudes.domain.response.save.SaveUserResponse;
import com.claudes.domain.response.superusers.SuperUsersResponse;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;

import java.util.List;

@RequestMapping("/users")
public interface IUserController {

    @PostMapping
    ResponseEntity<SaveUserResponse> saveUser(@RequestBody List<UserRequest> request);

    @GetMapping("/all")
    ResponseEntity<List<UserRequest>> getAllUsers();

    @GetMapping("/superusers")
    ResponseEntity<List<SuperUsersResponse>> getSuperUsers();
}

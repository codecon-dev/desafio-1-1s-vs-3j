package com.claudes.controller;

import com.claudes.domain.request.UserRequest;
import com.claudes.domain.response.save.SaveUserResponse;
import com.claudes.domain.response.superusers.SuperUsersResponse;
import com.claudes.domain.response.topcountries.CountriesResponse;
import com.claudes.domain.response.topcountries.TopCountriesResponse;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.RestController;

import java.time.Instant;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.stream.Collectors;

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
    public ResponseEntity<SuperUsersResponse> getSuperUsers() {
        long startTime = System.currentTimeMillis();
        var timestamp = Instant.now().toString();

        var response = new SuperUsersResponse();
        var supserUsers = users.stream()
                .filter(userRequest -> userRequest.getScore() >= 900 && userRequest.getActive())
                .toList();

        long endTime = System.currentTimeMillis();
        long executionTime = endTime - startTime;

        response.setData(supserUsers);
        response.setTimestamp(timestamp);
        response.setExecution_time_ms(executionTime);

        return ResponseEntity.ok(response);
    }

    @Override
    public ResponseEntity<TopCountriesResponse> getTopCountries() {
        long startTime = System.currentTimeMillis();
        var timestamp = Instant.now().toString();

        var response = new TopCountriesResponse();
        Map<String, Long> totalPorPais = users.stream()
                .collect(Collectors.groupingBy(
                        UserRequest::getCountry,
                        Collectors.counting()
                ));

        var totalContriues = totalPorPais.keySet().stream().map(countrie -> {
            var countrieResponse = new CountriesResponse(countrie, totalPorPais.get(countrie));
            return countrieResponse;
        }).toList();

        long endTime = System.currentTimeMillis();
        long executionTime = endTime - startTime;

        response.setCountries(totalContriues);
        response.setTimestamp(timestamp);
        response.setExecution_time_ms(executionTime);

        return ResponseEntity.ok(response);
    }


}

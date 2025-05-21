package com.claudes.domain.response.topcountries;

import com.claudes.domain.response.ResponseDefault;

import java.util.List;

public class TopCountriesResponse extends ResponseDefault {
    private List<CountriesResponse> countries;

    public TopCountriesResponse() {
    }

    public TopCountriesResponse(List<CountriesResponse> countries) {
        this.countries = countries;
    }

    public TopCountriesResponse(String timestamp, int execution_time_ms, List<CountriesResponse> countries) {
        super(timestamp, execution_time_ms);
        this.countries = countries;
    }

    public List<CountriesResponse> getCountries() {
        return countries;
    }

    public void setCountries(List<CountriesResponse> countries) {
        this.countries = countries;
    }
}

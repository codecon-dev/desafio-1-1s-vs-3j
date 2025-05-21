package com.claudes.domain.response.topcountries;

public class CountriesResponse {

    private String country;
    private long total;

    public CountriesResponse() {
    }

    public CountriesResponse(String country, long total) {
        this.country = country;
        this.total = total;
    }

    public String getCountry() {
        return country;
    }

    public void setCountry(String country) {
        this.country = country;
    }

    public long getTotal() {
        return total;
    }

    public void setTotal(long total) {
        this.total = total;
    }
}

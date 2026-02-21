<?php

test("the application returns a successful response", function () {
    $response = $this->get("/");

    $response->assertOk();
    $response->assertSee("User Login");
    $response->assertSee("User Register");
    $response->assertSee("Admin Login");
});

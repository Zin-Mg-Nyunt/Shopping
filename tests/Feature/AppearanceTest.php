<?php

test('appearance defaults to light when no cookie is set', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee("const appearance = 'light'", false);
});

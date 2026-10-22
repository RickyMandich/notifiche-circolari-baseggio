<?php

test('la home reindirizza alla gestione iscritti', function () {
    $this->get('/')->assertRedirect('/iscritti');
});

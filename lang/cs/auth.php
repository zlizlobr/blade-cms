<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines (Czech)
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user.
    |
    */

    'failed' => 'Tyto přihlašovací údaje neodpovídají žádnému záznamu.',
    'password' => 'Zadané heslo je nesprávné.',
    'throttle' => 'Příliš mnoho pokusů o přihlášení. Zkuste to prosím znovu za :seconds sekund.',

    // Login page
    'login' => [
        'title' => 'Přihlásit se',
        'email' => 'E-mail',
        'password' => 'Heslo',
        'remember' => 'Zapamatovat si mě',
        'forgot_password' => 'Zapomněli jste heslo?',
        'submit' => 'Přihlásit se',
    ],

    // Register page
    'register' => [
        'title' => 'Zaregistrovat se',
        'name' => 'Jméno',
        'email' => 'E-mail',
        'password' => 'Heslo',
        'confirm_password' => 'Potvrdit heslo',
        'already_registered' => 'Již jste zaregistrováni?',
        'submit' => 'Zaregistrovat se',
    ],

    // Forgot password
    'forgot_password' => [
        'title' => 'Zapomenuté heslo',
        'description' => 'Zapomněli jste heslo? Žádný problém. Jen nám sdělte vaši e-mailovou adresu a my vám zašleme odkaz pro reset hesla.',
        'email' => 'E-mail',
        'submit' => 'Odeslat odkaz pro reset hesla',
    ],

    // Reset password
    'reset_password' => [
        'title' => 'Resetovat heslo',
        'email' => 'E-mail',
        'password' => 'Heslo',
        'confirm_password' => 'Potvrdit heslo',
        'submit' => 'Resetovat heslo',
    ],

    // Confirm password
    'confirm_password' => [
        'title' => 'Potvrdit heslo',
        'description' => 'Toto je zabezpečená oblast aplikace. Prosím potvrďte své heslo před pokračováním.',
        'password' => 'Heslo',
        'submit' => 'Potvrdit',
    ],

    // Verify email
    'verify_email' => [
        'title' => 'Ověřit e-mailovou adresu',
        'description' => 'Děkujeme za registraci! Před začátkem práce prosím ověřte svou e-mailovou adresu kliknutím na odkaz, který jsme vám právě poslali. Pokud jste e-mail neobdrželi, rádi vám ho pošleme znovu.',
        'resend' => 'Znovu odeslat ověřovací e-mail',
        'logout' => 'Odhlásit se',
        'sent' => 'Nový ověřovací odkaz byl odeslán na vaši e-mailovou adresu.',
    ],

];

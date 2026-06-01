<?php

namespace App\Service;

/**
 * Helper pour les fonctions de debug conditionnelles
 * Désactive automatiquement les dumps/logs selon l'environnement
 */
class DebugHelper
{
    private string $environment;

    public function __construct(string $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Dump conditionnel - ne s'affiche qu'en dev
     */
    public function dump(...$vars): void
    {
        if ($this->environment === 'dev') {
            dump(...$vars);
        }
    }

    /**
     * DD conditionnel - ne s'affiche qu'en dev
     */
    public function dd(...$vars): void
    {
        if ($this->environment === 'dev') {
            dd(...$vars);
        }
    }

    /**
     * Log conditionnel pour debug - ne log qu'en dev
     */
    public function log(string $message, array $context = []): void
    {
        if ($this->environment === 'dev') {
            error_log("DEBUG: " . $message . " " . json_encode($context));
        }
    }

    /**
     * Vérifie si on est en mode debug
     */
    public function isDebugMode(): bool
    {
        return $this->environment === 'dev';
    }
}
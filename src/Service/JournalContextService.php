<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class JournalContextService
{
    private ?string $currentJournalCode = null;
    
    public function __construct(
        private RequestStack $requestStack,
        #[Autowire(env: 'RVCODE')] private ?string $envRvCode = null
    ) {
    }
    
    public function getCurrentJournalCode(): string
    {
        if ($this->currentJournalCode !== null) {
            return $this->currentJournalCode;
        }
        
        // Priority 1: Route parameter (if explicitly defined)
        $request = $this->requestStack->getCurrentRequest();
        if ($request && $request->attributes->has('code')) {
            $this->currentJournalCode = $request->attributes->get('code');
            return $this->currentJournalCode;
        }
        
        // Priority 2: Environment variable RVCODE
        if ($this->envRvCode) {
            $this->currentJournalCode = $this->envRvCode;
            return $this->currentJournalCode;
        }
        
        // Priority 3: Extract from HTTP host (subdomain)
        if ($request) {
            $host = $request->getHost();
            if (preg_match('/^([a-zA-Z0-9-]+)\.episciences\.org$/', $host, $matches)) {
                $this->currentJournalCode = $matches[1];
                return $this->currentJournalCode;
            }
        }
        
        // Fallback: throw exception if cannot determine
        throw new \RuntimeException('Cannot determine journal code from request, environment or host');
    }
    
    public function setCurrentJournalCode(string $code): void
    {
        $this->currentJournalCode = $code;
    }
    
    public function clearCache(): void
    {
        $this->currentJournalCode = null;
    }
}
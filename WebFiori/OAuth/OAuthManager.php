<?php
namespace WebFiori\OAuth;

use WebFiori\OAuth\Providers\Provider;
use WebFiori\OAuth\Storage\TokenStorage;
use WebFiori\OAuth\Exceptions\OAuth2Exception;

/**
 * OAuth2 provider manager for handling multiple OAuth2 providers.
 */
class OAuthManager {
    private array $providers = [];
    private ?TokenStorage $storage = null;

    /**
     * Create new OAuth manager.
     * 
     * @param TokenStorage|null $storage Token storage instance
     */
    public function __construct(?TokenStorage $storage = null) {
        $this->storage = $storage;
    }

    /**
     * Register an OAuth2 provider.
     * 
     * @param string $name Provider name
     * @param Provider $provider Provider instance
     * @return self
     */
    public function addProvider(string $name, Provider $provider): self {
        $this->providers[$name] = $provider;
        return $this;
    }

    /**
     * Get OAuth2 client for a provider.
     * 
     * @param string $name Provider name
     * @return OAuth2Client
     * @throws OAuth2Exception If provider not found
     */
    public function getClient(string $name): OAuth2Client {
        if (!isset($this->providers[$name])) {
            throw new OAuth2Exception("Provider '$name' not found");
        }
        
        return new OAuth2Client($this->providers[$name], $this->storage);
    }

    /**
     * Check if provider is registered.
     * 
     * @param string $name Provider name
     * @return bool
     */
    public function hasProvider(string $name): bool {
        return isset($this->providers[$name]);
    }

    /**
     * Get all registered provider names.
     * 
     * @return array
     */
    public function getProviderNames(): array {
        return array_keys($this->providers);
    }

    /**
     * Remove a provider.
     * 
     * @param string $name Provider name
     * @return self
     */
    public function removeProvider(string $name): self {
        unset($this->providers[$name]);
        return $this;
    }

    /**
     * Set token storage for all clients.
     * 
     * @param TokenStorage $storage Token storage instance
     * @return self
     */
    public function setStorage(TokenStorage $storage): self {
        $this->storage = $storage;
        return $this;
    }
}

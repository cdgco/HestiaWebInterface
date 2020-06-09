<?php

namespace Auth0\SDK\API\Helpers;

/**
 * Class InformationHeaders
 * Builds, extends, modifies, and formats SDK telemetry data.
 *
 * @package Auth0\SDK\API\Helpers
 */
class InformationHeaders
{

    /**
     * Default header data to send.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Set the main SDK name and version.
     *
     * @param string $name    SDK name.
     * @param string $version SDK version number.
     *
     * @return void
     */
    public function setPackage($name, $version)
    {
        $this->data['name']    = $name;
        $this->data['version'] = $version;
    }

    /**
     * Set the main SDK name and version to the PHP SDK.
     *
     * @return void
     */
    public function setCorePackage()
    {
        $this->setPackage('auth0-php', ApiClient::API_VERSION);
        $this->setEnvProperty('php', phpversion());
    }

    /**
     * Add an optional env property for SDK telemetry.
     *
     * @param string $name    Property name to set, name of dependency or platform.
     * @param string $version Version number of dependency or platform.
     *
     * @return void
     */
    public function setEnvProperty($name, $version)
    {
        if (! isset($this->data['env']) || ! is_array($this->data['env'])) {
            $this->data['env'] = [];
        }

        $this->data['env'][$name] = $version;
    }

    /**
     * Replace the current env data with new data.
     *
     * @param array $data Env data to add.
     *
     * @return void
     */
    public function setEnvironmentData(array $data)
    {
        $this->data['env'] = $data;
    }

    /**
     * Get the current header data as an array.
     *
     * @return array
     */
    public function get()
    {
        return $this->data;
    }

    /**
     * Return a header-formatted string.
     *
     * @return string
     */
    public function build()
    {
        return base64_encode(json_encode($this->get()));
    }

    /**
     * Extend an existing InformationHeaders object.
     * Used in dependant modules to set a new SDK name and version but keep existing PHP SDK data.
     *
     * @param InformationHeaders $headers InformationHeaders object to extend.
     *
     * @return InformationHeaders
     */
    public static function Extend(InformationHeaders $headers)
    {
        $new_headers = new InformationHeaders;
        $old_headers = $headers->get();

        if (! empty( $old_headers['env'] ) && is_array( $old_headers['env'] )) {
            $new_headers->setEnvironmentData($old_headers['env']);
        }

        $new_headers->setEnvProperty($old_headers['name'], $old_headers['version']);

        return $new_headers;
    }

    /*
     * Deprecated
     */

    // phpcs:disable

    /**
     * @deprecated 5.4.0, use $this->setEnvProperty() instead.
     *
     * @param string $name    Dependency or platform name.
     * @param string $version Dependency or platform version.
     *
     * @return void
     *
     * @codeCoverageIgnore - Deprecated
     */
    public function setEnvironment($name, $version)
    {
        $this->setEnvProperty($name, $version);
    }

    /**
     * @deprecated 5.4.0, use $this->setEnvProperty() instead.
     *
     * @param string $name    Dependency name.
     * @param string $version Dependency version.
     *
     * @return void
     *
     * @codeCoverageIgnore - Deprecated
     */
    public function setDependency($name, $version)
    {
        $this->setEnvProperty($name, $version);
    }

    /**
     * @deprecated 5.4.0, use $this->setEnvProperty() instead.
     *
     * @param array $data Dependency data to store.
     *
     * @return void
     *
     * @codeCoverageIgnore - Deprecated
     */
    public function setDependencyData(array $data)
    {
        $this->data['dependencies'] = $data;
    }

    // phpcs:enable
}

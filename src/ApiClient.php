<?php
/**
 * @author boxtal <api@boxtal.com>
 * @copyright 2018 Boxtal
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
namespace Boxtal\BoxtalPhp;

/**
 * Class ApiClient
 * @package Boxtal\BoxtalPhp
 *
 *  Facilitates Boxtal API calls.
 */
class ApiClient
{

    /**
     * Public key.
     *
     * @var RestClient
     */
    public $restClient;

    /**
     * Construct function.
     *
     * @param string $accessKey access key.
     * @param string $secretKey secret key.
     * @void
     */
    public function __construct($accessKey, $secretKey)
    {
        $this->restClient = new RestClient($accessKey, $secretKey);
    }

    /**
     * Get api url.
     *
     * @return string
     */
    public function getApiUrl()
    {
        $content = file_get_contents(__DIR__ . '/config.json');
        $config = json_decode($content);
        return $config->apiUrl;
    }
}

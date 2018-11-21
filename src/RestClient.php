<?php
namespace Boxtal\BoxtalPhp;

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

/**
 * Class RestClient
 * @package Boxtal\BoxtalPhp
 *
 *  Facilitates REST calls.
 */
class RestClient
{

    /**
     * Access key.
     *
     * @var string
     */
    private $accessKey;

    /**
     * Secret key.
     *
     * @var string
     */
    private $secretKey;

    public static $GET = 'GET';
    public static $POST = 'POST';
    public static $PUT = 'PUT';
    public static $PATCH = 'PATCH';
    public static $DELETE = 'DELETE';

    /**
     * Construct function.
     *
     * @param string $accessKey access key.
     * @param string $secretKey secret key.
     * @void
     */
    public function __construct($accessKey, $secretKey)
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
    }

    /**
     * API request
     *
     * @param string $method one of GET, POST, PUT, PATCH, DELETE.
     * @param string $url url for the request.
     * @param array $params array of params.
     * @param array $headers array of headers.
     * @param int $timeout timeout in seconds.
     * @return ApiResponse
     */
    public function request($method, $url, $params = array(), $headers = array(), $timeout = null)
    {

        $headers['Authorization'] = 'Basic '.base64_encode($this->accessKey . ':' . $this->secretKey);
        $headers['Content-type'] = 'application/json; charset=UTF-8';

        $header = '';
        foreach ($headers as $key => $value) {
            $header .= $key . ': ' . $value ."\r\n";
        }

        $opts = array(
            'http' => array(
                'method'  => $method,
                'header'  => $header,
                'content' => $method !== $this::$GET ? json_encode($params) : null
            )
        );

        if ($timeout !== null) {
            $opts['http']['timeout'] = $timeout;
        }

        $context = stream_context_create($opts);

        if ($method === $this::$GET && !empty($params)) {
            if (false === strpos($url, '?')) {
                $url .= '?';
            } else {
                $url .= '&';
            }
            $url .= http_build_query($params);
            $url = preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', $url);
        }

        $stream = @fopen($url, 'r', false, $context);

        if (false === $stream) {
            $return = new ApiResponse(400, null);
        } else {
            if ($this->isJsonContentType($http_response_header)) {
                $response = json_decode(stream_get_contents($stream));
            } else {
                $response = stream_get_contents($stream);
            }

            $return = new ApiResponse($this->getStreamStatus($stream), $response);

            fclose($stream);
        }

        return $return;
    }

    /**
     * Get stream status
     *
     * @return string
     */
    private function getStreamStatus($stream)
    {
        $data = stream_get_meta_data($stream);
        $wrapperLines = $data['wrapper_data'];
        $matches = array();
        for ($i = count($wrapperLines); $i >= 1; $i--) {
            if (0 === strpos($wrapperLines[$i - 1], 'HTTP/1')) {
                preg_match('/(\d{3})/', $wrapperLines[$i - 1], $matches);
                break;
            }
        }
        return empty($matches) ? null : $matches[1];
    }

    /**
     * Check if content type is json
     *
     * @param array string response headers
     * @return boolean
     */
    private function isJsonContentType($httpResponseHeaders) {
        $return = false;
        foreach ($httpResponseHeaders as $header) {
            if (-1 !== strpos('Content-Type: application/json', $header)) {
                $return = true;
            }
        }
        return $return;
    }
}

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * API Documentation Interactive Testing
 * FastAPI-style "Try it out" functionality
 */

(function () {
    'use strict';

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function () {
        initializeCollapsibleEndpoints();
        initializeTryItOut();
        initializeCopyButtons();
    });

    /**
     * Initialize collapsible endpoint sections
     */
    function initializeCollapsibleEndpoints() {
        const headers = document.querySelectorAll('.endpoint-header');

        headers.forEach(function (header) {
            header.addEventListener('click', function () {
                const content = this.nextElementSibling;

                // Toggle collapsed class
                this.classList.toggle('collapsed');
                content.classList.toggle('collapsed');
            });
        });
    }

    /**
     * Initialize "Try it out" functionality
     */
    function initializeTryItOut() {
        const tryItBtns = document.querySelectorAll('.try-it-btn:not(.execute)');

        tryItBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const section = this.closest('.try-it-out');
                const form = section.querySelector('.try-it-form');

                if (form.style.display === 'none' || !form.style.display) {
                    form.style.display = 'block';
                    this.textContent = 'Cancel';
                } else {
                    form.style.display = 'none';
                    this.textContent = 'Try it out';
                }
            });
        });

        // Execute button handlers
        const executeBtns = document.querySelectorAll('.try-it-btn.execute');

        executeBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                executeRequest(this);
            });
        });
    }

    /**
     * Execute API request
     */
    function executeRequest(button) {
        const section = button.closest('.try-it-out');
        const endpoint = section.dataset.endpoint;
        const method = section.dataset.method;
        const token = section.dataset.token;

        // Collect parameters
        const params = {};
        const inputs = section.querySelectorAll('.parameter-input input, .parameter-input textarea');

        inputs.forEach(function (input) {
            if (input.value) {
                params[input.name] = input.value;
            }
        });

        // Build URL
        let url = endpoint;
        const queryParams = [];
        const pathParamsUsed = new Set();

        // Replace path parameters and collect query parameters
        for (const [key, value] of Object.entries(params)) {
            // Create a regex to match the parameter in curly braces
            // This handles parameter names with spaces and special characters
            const paramPattern = new RegExp('\\{' + key.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\}', 'g');

            if (url.match(paramPattern)) {
                url = url.replace(paramPattern, encodeURIComponent(value));
                pathParamsUsed.add(key);
            } else if (method === 'GET') {
                queryParams.push(encodeURIComponent(key) + '=' + encodeURIComponent(value));
            }
        }

        if (queryParams.length > 0) {
            url += '?' + queryParams.join('&');
        }

        // Show loading state
        button.disabled = true;
        button.textContent = 'Executing...';

        // Prepare request options
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            }
        };

        // Only add Authorization header if token exists
        if (token && token.trim() !== '') {
            options.headers['Authorization'] = 'Bearer ' + token;
        }

        // Add body for POST/PUT/PATCH
        if (['POST', 'PUT', 'PATCH'].includes(method)) {
            const bodyParams = {};
            for (const [key, value] of Object.entries(params)) {
                // Only include parameters that weren't used in the path
                if (!pathParamsUsed.has(key)) {
                    bodyParams[key] = value;
                }
            }
            if (Object.keys(bodyParams).length > 0) {
                options.body = JSON.stringify(bodyParams);
            }
        }
        // Execute request
        fetch(url, options)
            .then(function (response) {
                const statusBadge = response.ok ? 'ressuccess' : 'reserror';
                const statusCode = response.status;
                const contentType = response.headers.get('content-type');

                // Check if response is JSON
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(function (data) {
                        return { data: data, status: statusCode, ok: response.ok };
                    }).catch(function () {
                        // If JSON parsing fails, return empty object
                        return { data: {}, status: statusCode, ok: response.ok };
                    });
                } else {
                    // For non-JSON responses, read as text
                    return response.text().then(function (text) {
                        return { data: text, status: statusCode, ok: response.ok };
                    });
                }
            })
            .then(function (result) {
                displayResponse(section, result, url, method, options);
            })
            .catch(function (error) {
                displayError(section, error);
            })
            .finally(function () {
                button.disabled = false;
                button.textContent = 'Execute';
            });
    }

    /**
     * Display API response
     */
    function displayResponse(section, result, url, method, options) {
        const responseSection = section.querySelector('.response-section');

        if (!responseSection) return;

        // Status badge
        const statusClass = result.ok ? 'ressuccess' : 'reserror';
        const statusHTML = '<span class="status-badge ' + statusClass + '">' + result.status + '</span>';

        // Response body
        let responseBody = result.data;
        if (typeof responseBody === 'object') {
            responseBody = JSON.stringify(responseBody, null, 2);
        }

        // cURL command
        const curlCommand = generateCurlCommand(url, method, options);

        // cURL command
        const nhRemote = generatenhRemoteCommand(url, method, options);

        // Update display
        responseSection.innerHTML = `
			<h5>Response ${statusHTML}</h5>
			<div class="response-display">
				<pre><code>${escapeHtml(responseBody)}</code></pre>
			</div>
			<h5 style="margin-top: 1.5rem;">cURL Command</h5>
			<div class="curl-command">
				<button class="copy-btn" onclick="copyToClipboard(this)">Copy</button>
				<pre><code>${escapeHtml(curlCommand)}</code></pre>
			</div>
			<h5 style="margin-top: 1.5rem;">nanohub-remote Command</h5>
			<div class="python-command">
				<button class="copy-btn" onclick="copyToClipboard(this)">Copy</button>
				<pre><code>${escapeHtml(nhRemote)}</code></pre>
			</div>
		`;

        responseSection.style.display = 'block';
    }

    /**
     * Display error
     */
    function displayError(section, error) {
        const responseSection = section.querySelector('.response-section');

        if (!responseSection) return;

        responseSection.innerHTML = `
			<h5>Error <span class="status-badge reserror">Failed</span></h5>
			<div class="response-display">
				<pre><code>${escapeHtml(error.message || 'Request failed')}</code></pre>
			</div>
		`;

        responseSection.style.display = 'block';
    }

    /**
     * Generate cURL command
     */
    function generateCurlCommand(url, method, options) {
        let curl = 'curl -X ' + method + ' \\\n  "' + url + '"';

        if (options.headers) {
            for (const [key, value] of Object.entries(options.headers)) {
                // Skip Authorization header if token is empty (i.e., value ends with "Bearer ")
                if (key === 'Authorization' && value.trim() === 'Bearer') {
                    continue;
                }
                if (value != "") {
                    curl += ' \\\n  -H "' + key + ': ' + value + '"';
                }
            }
        }

        if (options.body) {
            curl += ' \\\n  -d \'' + options.body + '\'';
        }

        return curl;
    }

    function generatenhRemoteCommand(url, method, options) {
        let code = 'import nanohubremote as nr\n\n';

        // Authentication setup
        let auth = "auth_data = {\n";
        auth += "  'client_id': 'XXXXXXXX',\n";
        auth += "  'client_secret': 'XXXXXXXX',\n";
        auth += "  'grant_type': 'password',\n";
        auth += "  'username': 'XXXXXXXX',\n";
        auth += "  'password': 'XXXXXXXX'\n";
        auth += "}\n";

        // Check if using personal token
        if (options.headers && options.headers['Authorization']) {
            try {
                const token = options.headers['Authorization'].split(" ")[1];
                auth = "auth_data = {\n";
                auth += "  'grant_type': 'personal_token',\n";
                auth += "  'token': '" + token + "'\n";
                auth += "}\n";
            } catch (error) {
                // Fall back to password auth
            }
        }

        code += auth;

        // Parse URL to separate base URL, path, and query parameters
        const urlObj = new URL(url);

        // Extract base URL up to and including /api
        const apiIndex = urlObj.pathname.indexOf('/api');
        let baseUrl = urlObj.origin;
        let endpointPath = urlObj.pathname;

        if (apiIndex !== -1) {
            // Include /api in the base URL
            baseUrl = urlObj.origin + urlObj.pathname.substring(0, apiIndex + 4);
            // Remove /api from the endpoint path
            endpointPath = urlObj.pathname.substring(apiIndex + 4);
        }

        code += "\nsession = nr.Session(auth_data, url='" + baseUrl + "')\n\n";

        // Extract query parameters from URL
        const queryParams = {};
        urlObj.searchParams.forEach((value, key) => {
            queryParams[key] = value;
        });

        // Note: nanohub-remote only supports GET and POST methods
        const supportedMethod = (method === 'GET' || method === 'POST') ? method : 'POST';

        if (supportedMethod !== method) {
            code += "# Note: nanohub-remote only supports GET and POST methods\n";
            code += "# Converting " + method + " to POST\n";
        }

        // Build data parameter combining body params and query params
        let dataObj = {};

        // Add query parameters to data
        if (Object.keys(queryParams).length > 0) {
            dataObj = { ...queryParams };
        }

        // Add body parameters to data (for POST/PUT/PATCH)
        if (options.body) {
            try {
                const bodyObj = JSON.parse(options.body);
                dataObj = { ...dataObj, ...bodyObj };
            } catch (e) {
                // Keep existing dataObj if body parsing fails
            }
        }

        code += "data = ";
        if (Object.keys(dataObj).length > 0) {
            code += JSON.stringify(dataObj, null, 2).replace(/"/g, "'");
        } else {
            code += "{}";
        }
        code += "\n\n";

        // Generate the appropriate request call (use endpoint path without /api prefix)
        if (supportedMethod === 'GET') {
            code += "response = session.requestGet(\n";
            code += "    '" + endpointPath + "',\n";
            code += "    data=data\n";
            code += ")\n";
        } else {
            code += "response = session.requestPost(\n";
            code += "    '" + endpointPath + "',\n";
            code += "    data=data\n";
            code += ")\n";
        }

        code += "\nprint(response.json())";

        return code;
    }


    /**
     * Initialize copy buttons
     */
    function initializeCopyButtons() {
        // Copy button functionality is handled inline in the HTML
        window.copyToClipboard = function (button) {
            const codeBlock = button.nextElementSibling.querySelector('code');
            const text = codeBlock.textContent;

            navigator.clipboard.writeText(text).then(function () {
                const originalText = button.textContent;
                button.textContent = 'Copied!';
                setTimeout(function () {
                    button.textContent = originalText;
                }, 2000);
            }).catch(function (err) {
                console.error('Failed to copy:', err);
            });
        };
    }

    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

})();

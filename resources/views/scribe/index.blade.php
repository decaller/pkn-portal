<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Laravel API Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://localhost";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.9.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.9.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-endpoints" class="tocify-header">
                <li class="tocify-item level-1" data-unique="endpoints">
                    <a href="#endpoints">Endpoints</a>
                </li>
                                    <ul id="tocify-subheader-endpoints" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="endpoints-POSTapi-auth-login">
                                <a href="#endpoints-POSTapi-auth-login">Login</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-auth-logout">
                                <a href="#endpoints-POSTapi-auth-logout">Logout</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-v1-mobile-dashboard">
                                <a href="#endpoints-GETapi-v1-mobile-dashboard">Get aggregated content for the mobile home screen.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-v1-events">
                                <a href="#endpoints-GETapi-v1-events">List all published events.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-v1-events--event_id-">
                                <a href="#endpoints-GETapi-v1-events--event_id-">Show a specific event.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-v1-events--event_id--similar">
                                <a href="#endpoints-GETapi-v1-events--event_id--similar">Show similar events.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-v1-news">
                                <a href="#endpoints-GETapi-v1-news">List all published news.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-v1-news--news_id-">
                                <a href="#endpoints-GETapi-v1-news--news_id-">Show a specific news article.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-v1-documents">
                                <a href="#endpoints-GETapi-v1-documents">List all active documents.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-v1-documents--document_id-">
                                <a href="#endpoints-GETapi-v1-documents--document_id-">Show a specific document.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-v1-auth-register">
                                <a href="#endpoints-POSTapi-v1-auth-register">POST api/v1/auth/register</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-v1-auth-login">
                                <a href="#endpoints-POSTapi-v1-auth-login">Authenticate a user via their phone number and password.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-v1-payments-webhook">
                                <a href="#endpoints-POSTapi-v1-payments-webhook">POST api/v1/payments/webhook</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-v1-auth-me">
                                <a href="#endpoints-GETapi-v1-auth-me">Get the authenticated user.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-v1-user">
                                <a href="#endpoints-GETapi-v1-user">Get the authenticated user.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-v1-auth-logout">
                                <a href="#endpoints-POSTapi-v1-auth-logout">Revoke the current token.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-v1-webview-ticket">
                                <a href="#endpoints-POSTapi-v1-webview-ticket">POST api/v1/webview-ticket</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-v1-user-profile">
                                <a href="#endpoints-GETapi-v1-user-profile">GET api/v1/user/profile</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-PUTapi-v1-user-profile">
                                <a href="#endpoints-PUTapi-v1-user-profile">PUT api/v1/user/profile</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-DELETEapi-v1-user-profile">
                                <a href="#endpoints-DELETEapi-v1-user-profile">DELETE api/v1/user/profile</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-v1-organizations">
                                <a href="#endpoints-GETapi-v1-organizations">GET api/v1/organizations</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-v1-organizations">
                                <a href="#endpoints-POSTapi-v1-organizations">POST api/v1/organizations</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-PUTapi-v1-organizations--id-">
                                <a href="#endpoints-PUTapi-v1-organizations--id-">PUT api/v1/organizations/{id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-DELETEapi-v1-organizations--id-">
                                <a href="#endpoints-DELETEapi-v1-organizations--id-">DELETE api/v1/organizations/{id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-v1-notifications">
                                <a href="#endpoints-GETapi-v1-notifications">GET api/v1/notifications</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-v1-notifications-unread-count">
                                <a href="#endpoints-GETapi-v1-notifications-unread-count">GET api/v1/notifications/unread-count</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-v1-notifications-mark-all-read">
                                <a href="#endpoints-POSTapi-v1-notifications-mark-all-read">POST api/v1/notifications/mark-all-read</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-v1-notifications--id--mark-read">
                                <a href="#endpoints-POSTapi-v1-notifications--id--mark-read">POST api/v1/notifications/{id}/mark-read</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-v1-registrations">
                                <a href="#endpoints-GETapi-v1-registrations">GET api/v1/registrations</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-v1-registrations">
                                <a href="#endpoints-POSTapi-v1-registrations">POST api/v1/registrations</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-v1-registrations--id-">
                                <a href="#endpoints-GETapi-v1-registrations--id-">GET api/v1/registrations/{id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-PUTapi-v1-registrations--id-">
                                <a href="#endpoints-PUTapi-v1-registrations--id-">PUT api/v1/registrations/{id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-DELETEapi-v1-registrations--id-">
                                <a href="#endpoints-DELETEapi-v1-registrations--id-">DELETE api/v1/registrations/{id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-v1-invoices">
                                <a href="#endpoints-GETapi-v1-invoices">GET api/v1/invoices</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-v1-invoices--invoice_id-">
                                <a href="#endpoints-GETapi-v1-invoices--invoice_id-">GET api/v1/invoices/{invoice_id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-v1-invoices--invoice_id--download">
                                <a href="#endpoints-GETapi-v1-invoices--invoice_id--download">GET api/v1/invoices/{invoice_id}/download</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-v1-payments-charge">
                                <a href="#endpoints-POSTapi-v1-payments-charge">POST api/v1/payments/charge</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: March 24, 2026</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>
<pre><code>This documentation aims to provide all the information you need to work with our API.

&lt;aside&gt;As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).&lt;/aside&gt;</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>This API is not authenticated.</p>

        <h1 id="endpoints">Endpoints</h1>

    

                                <h2 id="endpoints-POSTapi-auth-login">Login</h2>

<p>
</p>



<span id="example-requests-POSTapi-auth-login">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/auth/login" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"email\": \"gbailey@example.net\",
    \"password\": \"architecto\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/auth/login"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "email": "gbailey@example.net",
    "password": "architecto"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-auth-login">
</span>
<span id="execution-results-POSTapi-auth-login" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-auth-login"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-auth-login"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-auth-login" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-auth-login">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-auth-login" data-method="POST"
      data-path="api/auth/login"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-auth-login', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-auth-login"
                    onclick="tryItOut('POSTapi-auth-login');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-auth-login"
                    onclick="cancelTryOut('POSTapi-auth-login');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-auth-login"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/auth/login</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-auth-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-auth-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-auth-login"
               value="gbailey@example.net"
               data-component="body">
    <br>
<p>Must be a valid email address. Example: <code>gbailey@example.net</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-auth-login"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
        </form>

                    <h2 id="endpoints-POSTapi-auth-logout">Logout</h2>

<p>
</p>



<span id="example-requests-POSTapi-auth-logout">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/auth/logout" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/auth/logout"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-auth-logout">
</span>
<span id="execution-results-POSTapi-auth-logout" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-auth-logout"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-auth-logout"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-auth-logout" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-auth-logout">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-auth-logout" data-method="POST"
      data-path="api/auth/logout"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-auth-logout', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-auth-logout"
                    onclick="tryItOut('POSTapi-auth-logout');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-auth-logout"
                    onclick="cancelTryOut('POSTapi-auth-logout');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-auth-logout"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/auth/logout</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-auth-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-auth-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-v1-mobile-dashboard">Get aggregated content for the mobile home screen.</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-mobile-dashboard">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/mobile-dashboard" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/mobile-dashboard"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-mobile-dashboard">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
strict-transport-security: max-age=31536000; includeSubDomains
referrer-policy: no-referrer-when-downgrade
content-security-policy: default-src &#039;self&#039;; script-src &#039;self&#039; &#039;unsafe-inline&#039; &#039;unsafe-eval&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; style-src &#039;self&#039; &#039;unsafe-inline&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; img-src &#039;self&#039; data: https://*; font-src &#039;self&#039; data: https://*; connect-src &#039;self&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; frame-src &#039;self&#039; https://*
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;featured_events&quot;: [
        {
            &quot;id&quot;: 2,
            &quot;title&quot;: &quot;PKN Regional Workshop 2026&quot;,
            &quot;slug&quot;: &quot;pkn-regional-workshop-2026&quot;,
            &quot;summary&quot;: null,
            &quot;description&quot;: &quot;&lt;p&gt;Workshop discussion.&lt;/p&gt;&quot;,
            &quot;event_date&quot;: &quot;2026-09-04&quot;,
            &quot;city&quot;: &quot;Kabupaten Cikarang&quot;,
            &quot;province&quot;: &quot;Jawa Barat&quot;,
            &quot;nation&quot;: &quot;Indonesia&quot;,
            &quot;duration_days&quot;: 5,
            &quot;google_maps_url&quot;: null,
            &quot;cover_image&quot;: &quot;http://localhost/storage/event-covers/FLYER-PANDUAN-IMPLEMENTASI-1.png&quot;,
            &quot;photos&quot;: [],
            &quot;files&quot;: [],
            &quot;documentation&quot;: [
                &quot;http://localhost/storage/event-documentation/WhatsApp Image 2024-07-10 at 3.04.53 PM.jpeg&quot;,
                &quot;http://localhost/storage/event-documentation/WhatsApp Image 2024-07-11 at 8.37.16 PM.jpeg&quot;,
                &quot;http://localhost/storage/event-documentation/WhatsApp Image 2024-07-11 at 8.37.17 PM (1).jpeg&quot;
            ],
            &quot;is_published&quot;: true,
            &quot;allow_registration&quot;: true,
            &quot;max_capacity&quot;: 50,
            &quot;available_spots&quot;: 48,
            &quot;is_full&quot;: false,
            &quot;registration_packages&quot;: [
                {
                    &quot;id&quot;: 1,
                    &quot;name&quot;: &quot;Regular&quot;,
                    &quot;price&quot;: 75000,
                    &quot;max_quota&quot;: 30
                },
                {
                    &quot;id&quot;: 2,
                    &quot;name&quot;: &quot;VIP&quot;,
                    &quot;price&quot;: 200000,
                    &quot;max_quota&quot;: 20
                }
            ],
            &quot;rundown&quot;: [
                {
                    &quot;type&quot;: &quot;advanced&quot;,
                    &quot;data&quot;: {
                        &quot;title&quot;: &quot;Materi Sesi 1&quot;,
                        &quot;speaker&quot;: &quot;Ustad Abdul Kholiq&quot;,
                        &quot;session_files&quot;: [
                            &quot;events/pkn-regional-workshop-2026/sessions/0. LEMBAR OBSERVASI 2-PEMETAAN KARAKTER.pdf&quot;,
                            &quot;events/pkn-regional-workshop-2026/sessions/0. OBSERVASI HARIAN 40 PILAR (CONTOH ISIAN).pdf&quot;
                        ]
                    }
                }
            ],
            &quot;tags&quot;: null,
            &quot;proposal&quot;: &quot;http://localhost/storage/event-proposals/BOOKLET AKADEMI GURU PKN 4_20231221_132330_0000.pdf&quot;,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-24T06:18:58.000000Z&quot;,
            &quot;event_type&quot;: &quot;offline&quot;,
            &quot;survey_template_id&quot;: null,
            &quot;place&quot;: null,
            &quot;payment_instructions&quot;: null
        },
        {
            &quot;id&quot;: 1,
            &quot;title&quot;: &quot;PKN National Conference 2026&quot;,
            &quot;slug&quot;: &quot;pkn-national-conference-2026&quot;,
            &quot;summary&quot;: null,
            &quot;description&quot;: &quot;&lt;p&gt;Annual national offline conference by PKN.&lt;/p&gt;&quot;,
            &quot;event_date&quot;: &quot;2026-06-04&quot;,
            &quot;city&quot;: null,
            &quot;province&quot;: null,
            &quot;nation&quot;: null,
            &quot;duration_days&quot;: null,
            &quot;google_maps_url&quot;: null,
            &quot;cover_image&quot;: &quot;http://localhost/storage/event-covers/Flyer + Booklet AKG PKN Batch V.png&quot;,
            &quot;photos&quot;: [],
            &quot;files&quot;: [],
            &quot;documentation&quot;: [
                &quot;http://localhost/storage/event-documentation/WhatsApp Image 2024-07-10 at 3.04.52 PM.jpeg&quot;,
                &quot;http://localhost/storage/event-documentation/WhatsApp Image 2024-07-10 at 3.04.53 PM (1).jpeg&quot;
            ],
            &quot;is_published&quot;: true,
            &quot;allow_registration&quot;: true,
            &quot;max_capacity&quot;: null,
            &quot;available_spots&quot;: null,
            &quot;is_full&quot;: false,
            &quot;registration_packages&quot;: [
                {
                    &quot;id&quot;: 1,
                    &quot;name&quot;: &quot;Regular&quot;,
                    &quot;price&quot;: 100000
                },
                {
                    &quot;id&quot;: 2,
                    &quot;name&quot;: &quot;VIP&quot;,
                    &quot;price&quot;: 250000
                }
            ],
            &quot;rundown&quot;: [
                {
                    &quot;type&quot;: &quot;advanced&quot;,
                    &quot;data&quot;: {
                        &quot;title&quot;: &quot;Ju&quot;,
                        &quot;session_files&quot;: [
                            &quot;events/pkn-national-conference-2026/sessions/0. FORM PERENCANAAN PROYEK PEMBELAJARAN.pdf&quot;,
                            &quot;events/pkn-national-conference-2026/sessions/0. LEMBAR OBSERVASI 1-PERTUMBUHAN KARAKTER.pdf&quot;
                        ]
                    }
                }
            ],
            &quot;tags&quot;: null,
            &quot;proposal&quot;: &quot;http://localhost/storage/event-proposals/BOOKLET AKADEMI GURU PKN VI.pdf&quot;,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-24T06:18:58.000000Z&quot;,
            &quot;event_type&quot;: &quot;offline&quot;,
            &quot;survey_template_id&quot;: null,
            &quot;place&quot;: null,
            &quot;payment_instructions&quot;: null
        },
        {
            &quot;id&quot;: 5,
            &quot;title&quot;: &quot;Sed quas officiis suscipit.&quot;,
            &quot;slug&quot;: &quot;numquam-sit-doloribus-est-natus&quot;,
            &quot;summary&quot;: null,
            &quot;description&quot;: null,
            &quot;event_date&quot;: &quot;2026-04-22&quot;,
            &quot;city&quot;: null,
            &quot;province&quot;: null,
            &quot;nation&quot;: null,
            &quot;duration_days&quot;: null,
            &quot;google_maps_url&quot;: null,
            &quot;cover_image&quot;: null,
            &quot;photos&quot;: [],
            &quot;files&quot;: [],
            &quot;documentation&quot;: [],
            &quot;is_published&quot;: true,
            &quot;allow_registration&quot;: true,
            &quot;max_capacity&quot;: null,
            &quot;available_spots&quot;: null,
            &quot;is_full&quot;: false,
            &quot;registration_packages&quot;: [],
            &quot;rundown&quot;: null,
            &quot;tags&quot;: null,
            &quot;proposal&quot;: null,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;event_type&quot;: &quot;offline&quot;,
            &quot;survey_template_id&quot;: null,
            &quot;place&quot;: null,
            &quot;payment_instructions&quot;: null
        },
        {
            &quot;id&quot;: 4,
            &quot;title&quot;: &quot;PKN Mini Summit 2024&quot;,
            &quot;slug&quot;: &quot;pkn-mini-summit-2024&quot;,
            &quot;summary&quot;: null,
            &quot;description&quot;: null,
            &quot;event_date&quot;: &quot;2025-07-04&quot;,
            &quot;city&quot;: &quot;Bandung&quot;,
            &quot;province&quot;: &quot;Jawa Barat&quot;,
            &quot;nation&quot;: &quot;Indonesia&quot;,
            &quot;duration_days&quot;: null,
            &quot;google_maps_url&quot;: null,
            &quot;cover_image&quot;: null,
            &quot;photos&quot;: [],
            &quot;files&quot;: [],
            &quot;documentation&quot;: [],
            &quot;is_published&quot;: true,
            &quot;allow_registration&quot;: false,
            &quot;max_capacity&quot;: null,
            &quot;available_spots&quot;: null,
            &quot;is_full&quot;: false,
            &quot;registration_packages&quot;: [],
            &quot;rundown&quot;: null,
            &quot;tags&quot;: null,
            &quot;proposal&quot;: null,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;event_type&quot;: &quot;offline&quot;,
            &quot;survey_template_id&quot;: null,
            &quot;place&quot;: null,
            &quot;payment_instructions&quot;: null
        },
        {
            &quot;id&quot;: 3,
            &quot;title&quot;: &quot;PKN Recap 2023&quot;,
            &quot;slug&quot;: &quot;pkn-recap-2023&quot;,
            &quot;summary&quot;: null,
            &quot;description&quot;: null,
            &quot;event_date&quot;: &quot;2025-03-04&quot;,
            &quot;city&quot;: null,
            &quot;province&quot;: null,
            &quot;nation&quot;: null,
            &quot;duration_days&quot;: null,
            &quot;google_maps_url&quot;: null,
            &quot;cover_image&quot;: null,
            &quot;photos&quot;: [],
            &quot;files&quot;: [],
            &quot;documentation&quot;: [],
            &quot;is_published&quot;: true,
            &quot;allow_registration&quot;: false,
            &quot;max_capacity&quot;: null,
            &quot;available_spots&quot;: null,
            &quot;is_full&quot;: false,
            &quot;registration_packages&quot;: [],
            &quot;rundown&quot;: null,
            &quot;tags&quot;: null,
            &quot;proposal&quot;: null,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;event_type&quot;: &quot;offline&quot;,
            &quot;survey_template_id&quot;: null,
            &quot;place&quot;: null,
            &quot;payment_instructions&quot;: null
        }
    ],
    &quot;latest_news&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;title&quot;: &quot;Registration Open: PKN National Conference 2026&quot;,
            &quot;content&quot;: &quot;&lt;p&gt;Registration is now open for PKN National Conference 2026.&lt;/p&gt;&quot;,
            &quot;thumbnail&quot;: &quot;http://localhost/storage/news-thumbnails/WhatsApp Image 2026-03-12 at 1.14.05 PM.jpeg&quot;,
            &quot;is_published&quot;: true,
            &quot;event_id&quot;: null,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52+00:00&quot;
        },
        {
            &quot;id&quot;: 2,
            &quot;title&quot;: &quot;Registration Open: PKN Regional Workshop 2026&quot;,
            &quot;content&quot;: &quot;&lt;p&gt;Registration is now open for PKN Regional Workshop 2026.&lt;/p&gt;&quot;,
            &quot;thumbnail&quot;: &quot;http://localhost/storage/news-thumbnails/WhatsApp Image 2026-03-12 at 1.14.07 PM.jpeg&quot;,
            &quot;is_published&quot;: true,
            &quot;event_id&quot;: null,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52+00:00&quot;
        }
    ],
    &quot;testimonials&quot;: [],
    &quot;featured_documents&quot;: [
        {
            &quot;id&quot;: 27,
            &quot;title&quot;: &quot;1. Mengembalikan Pendidikan ke asalnya-1.pdf&quot;,
            &quot;slug&quot;: &quot;doc-a7z0y&quot;,
            &quot;file_url&quot;: &quot;http://localhost/storage/manual-uploads/0. PROGRAM PEMBELAJARAN 40 PILAR.xlsx&quot;,
            &quot;original_filename&quot;: &quot;0. PROGRAM PEMBELAJARAN 40 PILAR.xlsx&quot;,
            &quot;cover_image&quot;: &quot;http://localhost/storage/document-covers/WhatsApp Image 2026-03-12 at 1.49.09 PM(1).jpeg&quot;,
            &quot;mime_type&quot;: &quot;application/pdf&quot;,
            &quot;description&quot;: null,
            &quot;tags&quot;: [
                &quot;featured&quot;
            ],
            &quot;is_active&quot;: true,
            &quot;is_featured&quot;: true,
            &quot;event_id&quot;: null,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52+00:00&quot;
        },
        {
            &quot;id&quot;: 30,
            &quot;title&quot;: &quot;4. BAKAT - TB - 40.pptx&quot;,
            &quot;slug&quot;: &quot;doc-pvpme&quot;,
            &quot;file_url&quot;: &quot;http://localhost/storage/manual-uploads/0. FORM PERENCANAAN PROYEK PEMBELAJARAN.pdf&quot;,
            &quot;original_filename&quot;: &quot;0. FORM PERENCANAAN PROYEK PEMBELAJARAN.pdf&quot;,
            &quot;cover_image&quot;: null,
            &quot;mime_type&quot;: null,
            &quot;description&quot;: null,
            &quot;tags&quot;: [
                &quot;featured&quot;
            ],
            &quot;is_active&quot;: true,
            &quot;is_featured&quot;: true,
            &quot;event_id&quot;: null,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52+00:00&quot;
        },
        {
            &quot;id&quot;: 31,
            &quot;title&quot;: &quot;3. Pembelajaran Alamiyah.pptx&quot;,
            &quot;slug&quot;: &quot;doc-tqtyz&quot;,
            &quot;file_url&quot;: &quot;http://localhost/storage/manual-uploads/0. LEMBAR OBSERVASI 1-PERTUMBUHAN KARAKTER.pdf&quot;,
            &quot;original_filename&quot;: &quot;0. LEMBAR OBSERVASI 1-PERTUMBUHAN KARAKTER.pdf&quot;,
            &quot;cover_image&quot;: null,
            &quot;mime_type&quot;: null,
            &quot;description&quot;: null,
            &quot;tags&quot;: [
                &quot;featured&quot;
            ],
            &quot;is_active&quot;: true,
            &quot;is_featured&quot;: true,
            &quot;event_id&quot;: null,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52+00:00&quot;
        },
        {
            &quot;id&quot;: 34,
            &quot;title&quot;: &quot;0. PROGRAM PEMBELAJARAN 40 PILAR.xlsx&quot;,
            &quot;slug&quot;: &quot;doc-pnxci&quot;,
            &quot;file_url&quot;: &quot;http://localhost/storage/manual-uploads/1. 40 PILAR KARAKTER diurai dalam KURIKULUM.pdf&quot;,
            &quot;original_filename&quot;: &quot;1. 40 PILAR KARAKTER diurai dalam KURIKULUM.pdf&quot;,
            &quot;cover_image&quot;: null,
            &quot;mime_type&quot;: null,
            &quot;description&quot;: null,
            &quot;tags&quot;: [
                &quot;featured&quot;
            ],
            &quot;is_active&quot;: true,
            &quot;is_featured&quot;: true,
            &quot;event_id&quot;: null,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52+00:00&quot;
        }
    ],
    &quot;contact_info&quot;: {
        &quot;phone&quot;: null,
        &quot;whatsapp_url&quot;: null
    },
    &quot;alerts&quot;: [],
    &quot;stats&quot;: {
        &quot;active_registrations&quot;: 11,
        &quot;pending_payments&quot;: 9
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-mobile-dashboard" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-mobile-dashboard"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-mobile-dashboard"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-mobile-dashboard" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-mobile-dashboard">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-mobile-dashboard" data-method="GET"
      data-path="api/v1/mobile-dashboard"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-mobile-dashboard', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-mobile-dashboard"
                    onclick="tryItOut('GETapi-v1-mobile-dashboard');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-mobile-dashboard"
                    onclick="cancelTryOut('GETapi-v1-mobile-dashboard');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-mobile-dashboard"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/mobile-dashboard</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-mobile-dashboard"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-mobile-dashboard"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-v1-events">List all published events.</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-events">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/events" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/events"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-events">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
strict-transport-security: max-age=31536000; includeSubDomains
referrer-policy: no-referrer-when-downgrade
content-security-policy: default-src &#039;self&#039;; script-src &#039;self&#039; &#039;unsafe-inline&#039; &#039;unsafe-eval&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; style-src &#039;self&#039; &#039;unsafe-inline&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; img-src &#039;self&#039; data: https://*; font-src &#039;self&#039; data: https://*; connect-src &#039;self&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; frame-src &#039;self&#039; https://*
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 2,
            &quot;title&quot;: &quot;PKN Regional Workshop 2026&quot;,
            &quot;slug&quot;: &quot;pkn-regional-workshop-2026&quot;,
            &quot;summary&quot;: null,
            &quot;description&quot;: &quot;&lt;p&gt;Workshop discussion.&lt;/p&gt;&quot;,
            &quot;event_date&quot;: &quot;2026-09-04&quot;,
            &quot;city&quot;: &quot;Kabupaten Cikarang&quot;,
            &quot;province&quot;: &quot;Jawa Barat&quot;,
            &quot;nation&quot;: &quot;Indonesia&quot;,
            &quot;duration_days&quot;: 5,
            &quot;google_maps_url&quot;: null,
            &quot;cover_image&quot;: &quot;http://localhost/storage/event-covers/FLYER-PANDUAN-IMPLEMENTASI-1.png&quot;,
            &quot;photos&quot;: [],
            &quot;files&quot;: [],
            &quot;documentation&quot;: [
                &quot;http://localhost/storage/event-documentation/WhatsApp Image 2024-07-10 at 3.04.53 PM.jpeg&quot;,
                &quot;http://localhost/storage/event-documentation/WhatsApp Image 2024-07-11 at 8.37.16 PM.jpeg&quot;,
                &quot;http://localhost/storage/event-documentation/WhatsApp Image 2024-07-11 at 8.37.17 PM (1).jpeg&quot;
            ],
            &quot;is_published&quot;: true,
            &quot;allow_registration&quot;: true,
            &quot;max_capacity&quot;: 50,
            &quot;available_spots&quot;: 48,
            &quot;is_full&quot;: false,
            &quot;registration_packages&quot;: [
                {
                    &quot;id&quot;: 1,
                    &quot;name&quot;: &quot;Regular&quot;,
                    &quot;price&quot;: 75000,
                    &quot;max_quota&quot;: 30
                },
                {
                    &quot;id&quot;: 2,
                    &quot;name&quot;: &quot;VIP&quot;,
                    &quot;price&quot;: 200000,
                    &quot;max_quota&quot;: 20
                }
            ],
            &quot;rundown&quot;: [
                {
                    &quot;type&quot;: &quot;advanced&quot;,
                    &quot;data&quot;: {
                        &quot;title&quot;: &quot;Materi Sesi 1&quot;,
                        &quot;speaker&quot;: &quot;Ustad Abdul Kholiq&quot;,
                        &quot;session_files&quot;: [
                            &quot;events/pkn-regional-workshop-2026/sessions/0. LEMBAR OBSERVASI 2-PEMETAAN KARAKTER.pdf&quot;,
                            &quot;events/pkn-regional-workshop-2026/sessions/0. OBSERVASI HARIAN 40 PILAR (CONTOH ISIAN).pdf&quot;
                        ]
                    }
                }
            ],
            &quot;tags&quot;: null,
            &quot;proposal&quot;: &quot;http://localhost/storage/event-proposals/BOOKLET AKADEMI GURU PKN 4_20231221_132330_0000.pdf&quot;,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-24T06:18:58.000000Z&quot;,
            &quot;event_type&quot;: &quot;offline&quot;,
            &quot;survey_template_id&quot;: null,
            &quot;place&quot;: null,
            &quot;payment_instructions&quot;: null
        },
        {
            &quot;id&quot;: 1,
            &quot;title&quot;: &quot;PKN National Conference 2026&quot;,
            &quot;slug&quot;: &quot;pkn-national-conference-2026&quot;,
            &quot;summary&quot;: null,
            &quot;description&quot;: &quot;&lt;p&gt;Annual national offline conference by PKN.&lt;/p&gt;&quot;,
            &quot;event_date&quot;: &quot;2026-06-04&quot;,
            &quot;city&quot;: null,
            &quot;province&quot;: null,
            &quot;nation&quot;: null,
            &quot;duration_days&quot;: null,
            &quot;google_maps_url&quot;: null,
            &quot;cover_image&quot;: &quot;http://localhost/storage/event-covers/Flyer + Booklet AKG PKN Batch V.png&quot;,
            &quot;photos&quot;: [],
            &quot;files&quot;: [],
            &quot;documentation&quot;: [
                &quot;http://localhost/storage/event-documentation/WhatsApp Image 2024-07-10 at 3.04.52 PM.jpeg&quot;,
                &quot;http://localhost/storage/event-documentation/WhatsApp Image 2024-07-10 at 3.04.53 PM (1).jpeg&quot;
            ],
            &quot;is_published&quot;: true,
            &quot;allow_registration&quot;: true,
            &quot;max_capacity&quot;: null,
            &quot;available_spots&quot;: null,
            &quot;is_full&quot;: false,
            &quot;registration_packages&quot;: [
                {
                    &quot;id&quot;: 1,
                    &quot;name&quot;: &quot;Regular&quot;,
                    &quot;price&quot;: 100000
                },
                {
                    &quot;id&quot;: 2,
                    &quot;name&quot;: &quot;VIP&quot;,
                    &quot;price&quot;: 250000
                }
            ],
            &quot;rundown&quot;: [
                {
                    &quot;type&quot;: &quot;advanced&quot;,
                    &quot;data&quot;: {
                        &quot;title&quot;: &quot;Ju&quot;,
                        &quot;session_files&quot;: [
                            &quot;events/pkn-national-conference-2026/sessions/0. FORM PERENCANAAN PROYEK PEMBELAJARAN.pdf&quot;,
                            &quot;events/pkn-national-conference-2026/sessions/0. LEMBAR OBSERVASI 1-PERTUMBUHAN KARAKTER.pdf&quot;
                        ]
                    }
                }
            ],
            &quot;tags&quot;: null,
            &quot;proposal&quot;: &quot;http://localhost/storage/event-proposals/BOOKLET AKADEMI GURU PKN VI.pdf&quot;,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-24T06:18:58.000000Z&quot;,
            &quot;event_type&quot;: &quot;offline&quot;,
            &quot;survey_template_id&quot;: null,
            &quot;place&quot;: null,
            &quot;payment_instructions&quot;: null
        },
        {
            &quot;id&quot;: 5,
            &quot;title&quot;: &quot;Sed quas officiis suscipit.&quot;,
            &quot;slug&quot;: &quot;numquam-sit-doloribus-est-natus&quot;,
            &quot;summary&quot;: null,
            &quot;description&quot;: null,
            &quot;event_date&quot;: &quot;2026-04-22&quot;,
            &quot;city&quot;: null,
            &quot;province&quot;: null,
            &quot;nation&quot;: null,
            &quot;duration_days&quot;: null,
            &quot;google_maps_url&quot;: null,
            &quot;cover_image&quot;: null,
            &quot;photos&quot;: [],
            &quot;files&quot;: [],
            &quot;documentation&quot;: [],
            &quot;is_published&quot;: true,
            &quot;allow_registration&quot;: true,
            &quot;max_capacity&quot;: null,
            &quot;available_spots&quot;: null,
            &quot;is_full&quot;: false,
            &quot;registration_packages&quot;: [],
            &quot;rundown&quot;: null,
            &quot;tags&quot;: null,
            &quot;proposal&quot;: null,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;event_type&quot;: &quot;offline&quot;,
            &quot;survey_template_id&quot;: null,
            &quot;place&quot;: null,
            &quot;payment_instructions&quot;: null
        },
        {
            &quot;id&quot;: 4,
            &quot;title&quot;: &quot;PKN Mini Summit 2024&quot;,
            &quot;slug&quot;: &quot;pkn-mini-summit-2024&quot;,
            &quot;summary&quot;: null,
            &quot;description&quot;: null,
            &quot;event_date&quot;: &quot;2025-07-04&quot;,
            &quot;city&quot;: &quot;Bandung&quot;,
            &quot;province&quot;: &quot;Jawa Barat&quot;,
            &quot;nation&quot;: &quot;Indonesia&quot;,
            &quot;duration_days&quot;: null,
            &quot;google_maps_url&quot;: null,
            &quot;cover_image&quot;: null,
            &quot;photos&quot;: [],
            &quot;files&quot;: [],
            &quot;documentation&quot;: [],
            &quot;is_published&quot;: true,
            &quot;allow_registration&quot;: false,
            &quot;max_capacity&quot;: null,
            &quot;available_spots&quot;: null,
            &quot;is_full&quot;: false,
            &quot;registration_packages&quot;: [],
            &quot;rundown&quot;: null,
            &quot;tags&quot;: null,
            &quot;proposal&quot;: null,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;event_type&quot;: &quot;offline&quot;,
            &quot;survey_template_id&quot;: null,
            &quot;place&quot;: null,
            &quot;payment_instructions&quot;: null
        },
        {
            &quot;id&quot;: 3,
            &quot;title&quot;: &quot;PKN Recap 2023&quot;,
            &quot;slug&quot;: &quot;pkn-recap-2023&quot;,
            &quot;summary&quot;: null,
            &quot;description&quot;: null,
            &quot;event_date&quot;: &quot;2025-03-04&quot;,
            &quot;city&quot;: null,
            &quot;province&quot;: null,
            &quot;nation&quot;: null,
            &quot;duration_days&quot;: null,
            &quot;google_maps_url&quot;: null,
            &quot;cover_image&quot;: null,
            &quot;photos&quot;: [],
            &quot;files&quot;: [],
            &quot;documentation&quot;: [],
            &quot;is_published&quot;: true,
            &quot;allow_registration&quot;: false,
            &quot;max_capacity&quot;: null,
            &quot;available_spots&quot;: null,
            &quot;is_full&quot;: false,
            &quot;registration_packages&quot;: [],
            &quot;rundown&quot;: null,
            &quot;tags&quot;: null,
            &quot;proposal&quot;: null,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;event_type&quot;: &quot;offline&quot;,
            &quot;survey_template_id&quot;: null,
            &quot;place&quot;: null,
            &quot;payment_instructions&quot;: null
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;http://localhost/api/v1/events?page=1&quot;,
        &quot;last&quot;: &quot;http://localhost/api/v1/events?page=1&quot;,
        &quot;prev&quot;: null,
        &quot;next&quot;: null
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;from&quot;: 1,
        &quot;last_page&quot;: 1,
        &quot;links&quot;: [
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;&amp;laquo; Previous&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            },
            {
                &quot;url&quot;: &quot;http://localhost/api/v1/events?page=1&quot;,
                &quot;label&quot;: &quot;1&quot;,
                &quot;page&quot;: 1,
                &quot;active&quot;: true
            },
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;Next &amp;raquo;&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            }
        ],
        &quot;path&quot;: &quot;http://localhost/api/v1/events&quot;,
        &quot;per_page&quot;: 15,
        &quot;to&quot;: 5,
        &quot;total&quot;: 5
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-events" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-events"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-events"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-events" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-events">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-events" data-method="GET"
      data-path="api/v1/events"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-events', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-events"
                    onclick="tryItOut('GETapi-v1-events');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-events"
                    onclick="cancelTryOut('GETapi-v1-events');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-events"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/events</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-events"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-events"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-v1-events--event_id-">Show a specific event.</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-events--event_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/events/3" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/events/3"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-events--event_id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
strict-transport-security: max-age=31536000; includeSubDomains
referrer-policy: no-referrer-when-downgrade
content-security-policy: default-src &#039;self&#039;; script-src &#039;self&#039; &#039;unsafe-inline&#039; &#039;unsafe-eval&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; style-src &#039;self&#039; &#039;unsafe-inline&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; img-src &#039;self&#039; data: https://*; font-src &#039;self&#039; data: https://*; connect-src &#039;self&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; frame-src &#039;self&#039; https://*
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 3,
        &quot;title&quot;: &quot;PKN Recap 2023&quot;,
        &quot;slug&quot;: &quot;pkn-recap-2023&quot;,
        &quot;summary&quot;: null,
        &quot;description&quot;: null,
        &quot;event_date&quot;: &quot;2025-03-04&quot;,
        &quot;city&quot;: null,
        &quot;province&quot;: null,
        &quot;nation&quot;: null,
        &quot;duration_days&quot;: null,
        &quot;google_maps_url&quot;: null,
        &quot;cover_image&quot;: null,
        &quot;photos&quot;: [],
        &quot;files&quot;: [],
        &quot;documentation&quot;: [],
        &quot;is_published&quot;: true,
        &quot;allow_registration&quot;: false,
        &quot;max_capacity&quot;: null,
        &quot;available_spots&quot;: null,
        &quot;is_full&quot;: false,
        &quot;registration_packages&quot;: [],
        &quot;rundown&quot;: null,
        &quot;tags&quot;: null,
        &quot;proposal&quot;: null,
        &quot;created_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
        &quot;event_type&quot;: &quot;offline&quot;,
        &quot;survey_template_id&quot;: null,
        &quot;place&quot;: null,
        &quot;payment_instructions&quot;: null,
        &quot;testimonials&quot;: []
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-events--event_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-events--event_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-events--event_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-events--event_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-events--event_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-events--event_id-" data-method="GET"
      data-path="api/v1/events/{event_id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-events--event_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-events--event_id-"
                    onclick="tryItOut('GETapi-v1-events--event_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-events--event_id-"
                    onclick="cancelTryOut('GETapi-v1-events--event_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-events--event_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/events/{event_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-events--event_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-events--event_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>event_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="event_id"                data-endpoint="GETapi-v1-events--event_id-"
               value="3"
               data-component="url">
    <br>
<p>The ID of the event. Example: <code>3</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-v1-events--event_id--similar">Show similar events.</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-events--event_id--similar">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/events/3/similar" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/events/3/similar"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-events--event_id--similar">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
strict-transport-security: max-age=31536000; includeSubDomains
referrer-policy: no-referrer-when-downgrade
content-security-policy: default-src &#039;self&#039;; script-src &#039;self&#039; &#039;unsafe-inline&#039; &#039;unsafe-eval&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; style-src &#039;self&#039; &#039;unsafe-inline&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; img-src &#039;self&#039; data: https://*; font-src &#039;self&#039; data: https://*; connect-src &#039;self&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; frame-src &#039;self&#039; https://*
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 2,
            &quot;title&quot;: &quot;PKN Regional Workshop 2026&quot;,
            &quot;slug&quot;: &quot;pkn-regional-workshop-2026&quot;,
            &quot;summary&quot;: null,
            &quot;description&quot;: &quot;&lt;p&gt;Workshop discussion.&lt;/p&gt;&quot;,
            &quot;event_date&quot;: &quot;2026-09-04&quot;,
            &quot;city&quot;: &quot;Kabupaten Cikarang&quot;,
            &quot;province&quot;: &quot;Jawa Barat&quot;,
            &quot;nation&quot;: &quot;Indonesia&quot;,
            &quot;duration_days&quot;: 5,
            &quot;google_maps_url&quot;: null,
            &quot;cover_image&quot;: &quot;http://localhost/storage/event-covers/FLYER-PANDUAN-IMPLEMENTASI-1.png&quot;,
            &quot;photos&quot;: [],
            &quot;files&quot;: [],
            &quot;documentation&quot;: [
                &quot;http://localhost/storage/event-documentation/WhatsApp Image 2024-07-10 at 3.04.53 PM.jpeg&quot;,
                &quot;http://localhost/storage/event-documentation/WhatsApp Image 2024-07-11 at 8.37.16 PM.jpeg&quot;,
                &quot;http://localhost/storage/event-documentation/WhatsApp Image 2024-07-11 at 8.37.17 PM (1).jpeg&quot;
            ],
            &quot;is_published&quot;: true,
            &quot;allow_registration&quot;: true,
            &quot;max_capacity&quot;: 50,
            &quot;available_spots&quot;: 48,
            &quot;is_full&quot;: false,
            &quot;registration_packages&quot;: [
                {
                    &quot;id&quot;: 1,
                    &quot;name&quot;: &quot;Regular&quot;,
                    &quot;price&quot;: 75000,
                    &quot;max_quota&quot;: 30
                },
                {
                    &quot;id&quot;: 2,
                    &quot;name&quot;: &quot;VIP&quot;,
                    &quot;price&quot;: 200000,
                    &quot;max_quota&quot;: 20
                }
            ],
            &quot;rundown&quot;: [
                {
                    &quot;type&quot;: &quot;advanced&quot;,
                    &quot;data&quot;: {
                        &quot;title&quot;: &quot;Materi Sesi 1&quot;,
                        &quot;speaker&quot;: &quot;Ustad Abdul Kholiq&quot;,
                        &quot;session_files&quot;: [
                            &quot;events/pkn-regional-workshop-2026/sessions/0. LEMBAR OBSERVASI 2-PEMETAAN KARAKTER.pdf&quot;,
                            &quot;events/pkn-regional-workshop-2026/sessions/0. OBSERVASI HARIAN 40 PILAR (CONTOH ISIAN).pdf&quot;
                        ]
                    }
                }
            ],
            &quot;tags&quot;: null,
            &quot;proposal&quot;: &quot;http://localhost/storage/event-proposals/BOOKLET AKADEMI GURU PKN 4_20231221_132330_0000.pdf&quot;,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-24T06:18:58.000000Z&quot;,
            &quot;event_type&quot;: &quot;offline&quot;,
            &quot;survey_template_id&quot;: null,
            &quot;place&quot;: null,
            &quot;payment_instructions&quot;: null
        },
        {
            &quot;id&quot;: 1,
            &quot;title&quot;: &quot;PKN National Conference 2026&quot;,
            &quot;slug&quot;: &quot;pkn-national-conference-2026&quot;,
            &quot;summary&quot;: null,
            &quot;description&quot;: &quot;&lt;p&gt;Annual national offline conference by PKN.&lt;/p&gt;&quot;,
            &quot;event_date&quot;: &quot;2026-06-04&quot;,
            &quot;city&quot;: null,
            &quot;province&quot;: null,
            &quot;nation&quot;: null,
            &quot;duration_days&quot;: null,
            &quot;google_maps_url&quot;: null,
            &quot;cover_image&quot;: &quot;http://localhost/storage/event-covers/Flyer + Booklet AKG PKN Batch V.png&quot;,
            &quot;photos&quot;: [],
            &quot;files&quot;: [],
            &quot;documentation&quot;: [
                &quot;http://localhost/storage/event-documentation/WhatsApp Image 2024-07-10 at 3.04.52 PM.jpeg&quot;,
                &quot;http://localhost/storage/event-documentation/WhatsApp Image 2024-07-10 at 3.04.53 PM (1).jpeg&quot;
            ],
            &quot;is_published&quot;: true,
            &quot;allow_registration&quot;: true,
            &quot;max_capacity&quot;: null,
            &quot;available_spots&quot;: null,
            &quot;is_full&quot;: false,
            &quot;registration_packages&quot;: [
                {
                    &quot;id&quot;: 1,
                    &quot;name&quot;: &quot;Regular&quot;,
                    &quot;price&quot;: 100000
                },
                {
                    &quot;id&quot;: 2,
                    &quot;name&quot;: &quot;VIP&quot;,
                    &quot;price&quot;: 250000
                }
            ],
            &quot;rundown&quot;: [
                {
                    &quot;type&quot;: &quot;advanced&quot;,
                    &quot;data&quot;: {
                        &quot;title&quot;: &quot;Ju&quot;,
                        &quot;session_files&quot;: [
                            &quot;events/pkn-national-conference-2026/sessions/0. FORM PERENCANAAN PROYEK PEMBELAJARAN.pdf&quot;,
                            &quot;events/pkn-national-conference-2026/sessions/0. LEMBAR OBSERVASI 1-PERTUMBUHAN KARAKTER.pdf&quot;
                        ]
                    }
                }
            ],
            &quot;tags&quot;: null,
            &quot;proposal&quot;: &quot;http://localhost/storage/event-proposals/BOOKLET AKADEMI GURU PKN VI.pdf&quot;,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-24T06:18:58.000000Z&quot;,
            &quot;event_type&quot;: &quot;offline&quot;,
            &quot;survey_template_id&quot;: null,
            &quot;place&quot;: null,
            &quot;payment_instructions&quot;: null
        },
        {
            &quot;id&quot;: 5,
            &quot;title&quot;: &quot;Sed quas officiis suscipit.&quot;,
            &quot;slug&quot;: &quot;numquam-sit-doloribus-est-natus&quot;,
            &quot;summary&quot;: null,
            &quot;description&quot;: null,
            &quot;event_date&quot;: &quot;2026-04-22&quot;,
            &quot;city&quot;: null,
            &quot;province&quot;: null,
            &quot;nation&quot;: null,
            &quot;duration_days&quot;: null,
            &quot;google_maps_url&quot;: null,
            &quot;cover_image&quot;: null,
            &quot;photos&quot;: [],
            &quot;files&quot;: [],
            &quot;documentation&quot;: [],
            &quot;is_published&quot;: true,
            &quot;allow_registration&quot;: true,
            &quot;max_capacity&quot;: null,
            &quot;available_spots&quot;: null,
            &quot;is_full&quot;: false,
            &quot;registration_packages&quot;: [],
            &quot;rundown&quot;: null,
            &quot;tags&quot;: null,
            &quot;proposal&quot;: null,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;event_type&quot;: &quot;offline&quot;,
            &quot;survey_template_id&quot;: null,
            &quot;place&quot;: null,
            &quot;payment_instructions&quot;: null
        },
        {
            &quot;id&quot;: 4,
            &quot;title&quot;: &quot;PKN Mini Summit 2024&quot;,
            &quot;slug&quot;: &quot;pkn-mini-summit-2024&quot;,
            &quot;summary&quot;: null,
            &quot;description&quot;: null,
            &quot;event_date&quot;: &quot;2025-07-04&quot;,
            &quot;city&quot;: &quot;Bandung&quot;,
            &quot;province&quot;: &quot;Jawa Barat&quot;,
            &quot;nation&quot;: &quot;Indonesia&quot;,
            &quot;duration_days&quot;: null,
            &quot;google_maps_url&quot;: null,
            &quot;cover_image&quot;: null,
            &quot;photos&quot;: [],
            &quot;files&quot;: [],
            &quot;documentation&quot;: [],
            &quot;is_published&quot;: true,
            &quot;allow_registration&quot;: false,
            &quot;max_capacity&quot;: null,
            &quot;available_spots&quot;: null,
            &quot;is_full&quot;: false,
            &quot;registration_packages&quot;: [],
            &quot;rundown&quot;: null,
            &quot;tags&quot;: null,
            &quot;proposal&quot;: null,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-24T01:18:52.000000Z&quot;,
            &quot;event_type&quot;: &quot;offline&quot;,
            &quot;survey_template_id&quot;: null,
            &quot;place&quot;: null,
            &quot;payment_instructions&quot;: null
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-events--event_id--similar" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-events--event_id--similar"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-events--event_id--similar"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-events--event_id--similar" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-events--event_id--similar">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-events--event_id--similar" data-method="GET"
      data-path="api/v1/events/{event_id}/similar"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-events--event_id--similar', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-events--event_id--similar"
                    onclick="tryItOut('GETapi-v1-events--event_id--similar');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-events--event_id--similar"
                    onclick="cancelTryOut('GETapi-v1-events--event_id--similar');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-events--event_id--similar"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/events/{event_id}/similar</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-events--event_id--similar"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-events--event_id--similar"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>event_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="event_id"                data-endpoint="GETapi-v1-events--event_id--similar"
               value="3"
               data-component="url">
    <br>
<p>The ID of the event. Example: <code>3</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-v1-news">List all published news.</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-news">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/news" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/news"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-news">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
strict-transport-security: max-age=31536000; includeSubDomains
referrer-policy: no-referrer-when-downgrade
content-security-policy: default-src &#039;self&#039;; script-src &#039;self&#039; &#039;unsafe-inline&#039; &#039;unsafe-eval&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; style-src &#039;self&#039; &#039;unsafe-inline&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; img-src &#039;self&#039; data: https://*; font-src &#039;self&#039; data: https://*; connect-src &#039;self&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; frame-src &#039;self&#039; https://*
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;title&quot;: &quot;Registration Open: PKN National Conference 2026&quot;,
            &quot;content&quot;: &quot;&lt;p&gt;Registration is now open for PKN National Conference 2026.&lt;/p&gt;&quot;,
            &quot;thumbnail&quot;: &quot;http://localhost/storage/news-thumbnails/WhatsApp Image 2026-03-12 at 1.14.05 PM.jpeg&quot;,
            &quot;is_published&quot;: true,
            &quot;event_id&quot;: null,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52+00:00&quot;
        },
        {
            &quot;id&quot;: 2,
            &quot;title&quot;: &quot;Registration Open: PKN Regional Workshop 2026&quot;,
            &quot;content&quot;: &quot;&lt;p&gt;Registration is now open for PKN Regional Workshop 2026.&lt;/p&gt;&quot;,
            &quot;thumbnail&quot;: &quot;http://localhost/storage/news-thumbnails/WhatsApp Image 2026-03-12 at 1.14.07 PM.jpeg&quot;,
            &quot;is_published&quot;: true,
            &quot;event_id&quot;: null,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52+00:00&quot;
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;http://localhost/api/v1/news?page=1&quot;,
        &quot;last&quot;: &quot;http://localhost/api/v1/news?page=1&quot;,
        &quot;prev&quot;: null,
        &quot;next&quot;: null
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;from&quot;: 1,
        &quot;last_page&quot;: 1,
        &quot;links&quot;: [
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;&amp;laquo; Previous&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            },
            {
                &quot;url&quot;: &quot;http://localhost/api/v1/news?page=1&quot;,
                &quot;label&quot;: &quot;1&quot;,
                &quot;page&quot;: 1,
                &quot;active&quot;: true
            },
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;Next &amp;raquo;&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            }
        ],
        &quot;path&quot;: &quot;http://localhost/api/v1/news&quot;,
        &quot;per_page&quot;: 15,
        &quot;to&quot;: 2,
        &quot;total&quot;: 2
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-news" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-news"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-news"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-news" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-news">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-news" data-method="GET"
      data-path="api/v1/news"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-news', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-news"
                    onclick="tryItOut('GETapi-v1-news');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-news"
                    onclick="cancelTryOut('GETapi-v1-news');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-news"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/news</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-news"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-news"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-v1-news--news_id-">Show a specific news article.</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-news--news_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/news/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/news/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-news--news_id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
strict-transport-security: max-age=31536000; includeSubDomains
referrer-policy: no-referrer-when-downgrade
content-security-policy: default-src &#039;self&#039;; script-src &#039;self&#039; &#039;unsafe-inline&#039; &#039;unsafe-eval&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; style-src &#039;self&#039; &#039;unsafe-inline&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; img-src &#039;self&#039; data: https://*; font-src &#039;self&#039; data: https://*; connect-src &#039;self&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; frame-src &#039;self&#039; https://*
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;title&quot;: &quot;Registration Open: PKN National Conference 2026&quot;,
        &quot;content&quot;: &quot;&lt;p&gt;Registration is now open for PKN National Conference 2026.&lt;/p&gt;&quot;,
        &quot;thumbnail&quot;: &quot;http://localhost/storage/news-thumbnails/WhatsApp Image 2026-03-12 at 1.14.05 PM.jpeg&quot;,
        &quot;is_published&quot;: true,
        &quot;event_id&quot;: null,
        &quot;created_at&quot;: &quot;2026-03-24T01:18:52+00:00&quot;,
        &quot;event&quot;: null
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-news--news_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-news--news_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-news--news_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-news--news_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-news--news_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-news--news_id-" data-method="GET"
      data-path="api/v1/news/{news_id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-news--news_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-news--news_id-"
                    onclick="tryItOut('GETapi-v1-news--news_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-news--news_id-"
                    onclick="cancelTryOut('GETapi-v1-news--news_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-news--news_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/news/{news_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-news--news_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-news--news_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>news_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="news_id"                data-endpoint="GETapi-v1-news--news_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the news. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-v1-documents">List all active documents.</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-documents">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/documents" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/documents"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-documents">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
strict-transport-security: max-age=31536000; includeSubDomains
referrer-policy: no-referrer-when-downgrade
content-security-policy: default-src &#039;self&#039;; script-src &#039;self&#039; &#039;unsafe-inline&#039; &#039;unsafe-eval&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; style-src &#039;self&#039; &#039;unsafe-inline&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; img-src &#039;self&#039; data: https://*; font-src &#039;self&#039; data: https://*; connect-src &#039;self&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; frame-src &#039;self&#039; https://*
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;featured_documents&quot;: [
        {
            &quot;id&quot;: 27,
            &quot;title&quot;: &quot;1. Mengembalikan Pendidikan ke asalnya-1.pdf&quot;,
            &quot;slug&quot;: &quot;doc-a7z0y&quot;,
            &quot;file_url&quot;: &quot;http://localhost/storage/manual-uploads/0. PROGRAM PEMBELAJARAN 40 PILAR.xlsx&quot;,
            &quot;original_filename&quot;: &quot;0. PROGRAM PEMBELAJARAN 40 PILAR.xlsx&quot;,
            &quot;cover_image&quot;: &quot;http://localhost/storage/document-covers/WhatsApp Image 2026-03-12 at 1.49.09 PM(1).jpeg&quot;,
            &quot;mime_type&quot;: &quot;application/pdf&quot;,
            &quot;description&quot;: null,
            &quot;tags&quot;: [
                &quot;featured&quot;
            ],
            &quot;is_active&quot;: true,
            &quot;is_featured&quot;: true,
            &quot;event_id&quot;: null,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52+00:00&quot;
        },
        {
            &quot;id&quot;: 34,
            &quot;title&quot;: &quot;0. PROGRAM PEMBELAJARAN 40 PILAR.xlsx&quot;,
            &quot;slug&quot;: &quot;doc-pnxci&quot;,
            &quot;file_url&quot;: &quot;http://localhost/storage/manual-uploads/1. 40 PILAR KARAKTER diurai dalam KURIKULUM.pdf&quot;,
            &quot;original_filename&quot;: &quot;1. 40 PILAR KARAKTER diurai dalam KURIKULUM.pdf&quot;,
            &quot;cover_image&quot;: null,
            &quot;mime_type&quot;: null,
            &quot;description&quot;: null,
            &quot;tags&quot;: [
                &quot;featured&quot;
            ],
            &quot;is_active&quot;: true,
            &quot;is_featured&quot;: true,
            &quot;event_id&quot;: null,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52+00:00&quot;
        },
        {
            &quot;id&quot;: 30,
            &quot;title&quot;: &quot;4. BAKAT - TB - 40.pptx&quot;,
            &quot;slug&quot;: &quot;doc-pvpme&quot;,
            &quot;file_url&quot;: &quot;http://localhost/storage/manual-uploads/0. FORM PERENCANAAN PROYEK PEMBELAJARAN.pdf&quot;,
            &quot;original_filename&quot;: &quot;0. FORM PERENCANAAN PROYEK PEMBELAJARAN.pdf&quot;,
            &quot;cover_image&quot;: null,
            &quot;mime_type&quot;: null,
            &quot;description&quot;: null,
            &quot;tags&quot;: [
                &quot;featured&quot;
            ],
            &quot;is_active&quot;: true,
            &quot;is_featured&quot;: true,
            &quot;event_id&quot;: null,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52+00:00&quot;
        },
        {
            &quot;id&quot;: 31,
            &quot;title&quot;: &quot;3. Pembelajaran Alamiyah.pptx&quot;,
            &quot;slug&quot;: &quot;doc-tqtyz&quot;,
            &quot;file_url&quot;: &quot;http://localhost/storage/manual-uploads/0. LEMBAR OBSERVASI 1-PERTUMBUHAN KARAKTER.pdf&quot;,
            &quot;original_filename&quot;: &quot;0. LEMBAR OBSERVASI 1-PERTUMBUHAN KARAKTER.pdf&quot;,
            &quot;cover_image&quot;: null,
            &quot;mime_type&quot;: null,
            &quot;description&quot;: null,
            &quot;tags&quot;: [
                &quot;featured&quot;
            ],
            &quot;is_active&quot;: true,
            &quot;is_featured&quot;: true,
            &quot;event_id&quot;: null,
            &quot;created_at&quot;: &quot;2026-03-24T01:18:52+00:00&quot;
        }
    ],
    &quot;documents&quot;: {
        &quot;data&quot;: [
            {
                &quot;id&quot;: 27,
                &quot;title&quot;: &quot;1. Mengembalikan Pendidikan ke asalnya-1.pdf&quot;,
                &quot;slug&quot;: &quot;doc-a7z0y&quot;,
                &quot;file_url&quot;: &quot;http://localhost/storage/manual-uploads/0. PROGRAM PEMBELAJARAN 40 PILAR.xlsx&quot;,
                &quot;original_filename&quot;: &quot;0. PROGRAM PEMBELAJARAN 40 PILAR.xlsx&quot;,
                &quot;cover_image&quot;: &quot;http://localhost/storage/document-covers/WhatsApp Image 2026-03-12 at 1.49.09 PM(1).jpeg&quot;,
                &quot;mime_type&quot;: &quot;application/pdf&quot;,
                &quot;description&quot;: null,
                &quot;tags&quot;: [
                    &quot;featured&quot;
                ],
                &quot;is_active&quot;: true,
                &quot;is_featured&quot;: true,
                &quot;event_id&quot;: null,
                &quot;created_at&quot;: &quot;2026-03-24T01:18:52+00:00&quot;
            },
            {
                &quot;id&quot;: 34,
                &quot;title&quot;: &quot;0. PROGRAM PEMBELAJARAN 40 PILAR.xlsx&quot;,
                &quot;slug&quot;: &quot;doc-pnxci&quot;,
                &quot;file_url&quot;: &quot;http://localhost/storage/manual-uploads/1. 40 PILAR KARAKTER diurai dalam KURIKULUM.pdf&quot;,
                &quot;original_filename&quot;: &quot;1. 40 PILAR KARAKTER diurai dalam KURIKULUM.pdf&quot;,
                &quot;cover_image&quot;: null,
                &quot;mime_type&quot;: null,
                &quot;description&quot;: null,
                &quot;tags&quot;: [
                    &quot;featured&quot;
                ],
                &quot;is_active&quot;: true,
                &quot;is_featured&quot;: true,
                &quot;event_id&quot;: null,
                &quot;created_at&quot;: &quot;2026-03-24T01:18:52+00:00&quot;
            },
            {
                &quot;id&quot;: 30,
                &quot;title&quot;: &quot;4. BAKAT - TB - 40.pptx&quot;,
                &quot;slug&quot;: &quot;doc-pvpme&quot;,
                &quot;file_url&quot;: &quot;http://localhost/storage/manual-uploads/0. FORM PERENCANAAN PROYEK PEMBELAJARAN.pdf&quot;,
                &quot;original_filename&quot;: &quot;0. FORM PERENCANAAN PROYEK PEMBELAJARAN.pdf&quot;,
                &quot;cover_image&quot;: null,
                &quot;mime_type&quot;: null,
                &quot;description&quot;: null,
                &quot;tags&quot;: [
                    &quot;featured&quot;
                ],
                &quot;is_active&quot;: true,
                &quot;is_featured&quot;: true,
                &quot;event_id&quot;: null,
                &quot;created_at&quot;: &quot;2026-03-24T01:18:52+00:00&quot;
            },
            {
                &quot;id&quot;: 31,
                &quot;title&quot;: &quot;3. Pembelajaran Alamiyah.pptx&quot;,
                &quot;slug&quot;: &quot;doc-tqtyz&quot;,
                &quot;file_url&quot;: &quot;http://localhost/storage/manual-uploads/0. LEMBAR OBSERVASI 1-PERTUMBUHAN KARAKTER.pdf&quot;,
                &quot;original_filename&quot;: &quot;0. LEMBAR OBSERVASI 1-PERTUMBUHAN KARAKTER.pdf&quot;,
                &quot;cover_image&quot;: null,
                &quot;mime_type&quot;: null,
                &quot;description&quot;: null,
                &quot;tags&quot;: [
                    &quot;featured&quot;
                ],
                &quot;is_active&quot;: true,
                &quot;is_featured&quot;: true,
                &quot;event_id&quot;: null,
                &quot;created_at&quot;: &quot;2026-03-24T01:18:52+00:00&quot;
            }
        ],
        &quot;links&quot;: {
            &quot;first&quot;: &quot;http://localhost/api/v1/documents?page=1&quot;,
            &quot;last&quot;: &quot;http://localhost/api/v1/documents?page=1&quot;,
            &quot;prev&quot;: null,
            &quot;next&quot;: null
        },
        &quot;meta&quot;: {
            &quot;current_page&quot;: 1,
            &quot;from&quot;: 1,
            &quot;last_page&quot;: 1,
            &quot;links&quot;: [
                {
                    &quot;url&quot;: null,
                    &quot;label&quot;: &quot;&amp;laquo; Previous&quot;,
                    &quot;page&quot;: null,
                    &quot;active&quot;: false
                },
                {
                    &quot;url&quot;: &quot;http://localhost/api/v1/documents?page=1&quot;,
                    &quot;label&quot;: &quot;1&quot;,
                    &quot;page&quot;: 1,
                    &quot;active&quot;: true
                },
                {
                    &quot;url&quot;: null,
                    &quot;label&quot;: &quot;Next &amp;raquo;&quot;,
                    &quot;page&quot;: null,
                    &quot;active&quot;: false
                }
            ],
            &quot;path&quot;: &quot;http://localhost/api/v1/documents&quot;,
            &quot;per_page&quot;: 20,
            &quot;to&quot;: 4,
            &quot;total&quot;: 4
        }
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-documents" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-documents"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-documents"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-documents" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-documents">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-documents" data-method="GET"
      data-path="api/v1/documents"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-documents', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-documents"
                    onclick="tryItOut('GETapi-v1-documents');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-documents"
                    onclick="cancelTryOut('GETapi-v1-documents');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-documents"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/documents</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-documents"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-documents"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-v1-documents--document_id-">Show a specific document.</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-documents--document_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/documents/27" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/documents/27"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-documents--document_id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
strict-transport-security: max-age=31536000; includeSubDomains
referrer-policy: no-referrer-when-downgrade
content-security-policy: default-src &#039;self&#039;; script-src &#039;self&#039; &#039;unsafe-inline&#039; &#039;unsafe-eval&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; style-src &#039;self&#039; &#039;unsafe-inline&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; img-src &#039;self&#039; data: https://*; font-src &#039;self&#039; data: https://*; connect-src &#039;self&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; frame-src &#039;self&#039; https://*
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 27,
        &quot;title&quot;: &quot;1. Mengembalikan Pendidikan ke asalnya-1.pdf&quot;,
        &quot;slug&quot;: &quot;doc-a7z0y&quot;,
        &quot;file_url&quot;: &quot;http://localhost/storage/manual-uploads/0. PROGRAM PEMBELAJARAN 40 PILAR.xlsx&quot;,
        &quot;original_filename&quot;: &quot;0. PROGRAM PEMBELAJARAN 40 PILAR.xlsx&quot;,
        &quot;cover_image&quot;: &quot;http://localhost/storage/document-covers/WhatsApp Image 2026-03-12 at 1.49.09 PM(1).jpeg&quot;,
        &quot;mime_type&quot;: &quot;application/pdf&quot;,
        &quot;description&quot;: null,
        &quot;tags&quot;: [
            &quot;featured&quot;
        ],
        &quot;is_active&quot;: true,
        &quot;is_featured&quot;: true,
        &quot;event_id&quot;: null,
        &quot;created_at&quot;: &quot;2026-03-24T01:18:52+00:00&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-documents--document_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-documents--document_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-documents--document_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-documents--document_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-documents--document_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-documents--document_id-" data-method="GET"
      data-path="api/v1/documents/{document_id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-documents--document_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-documents--document_id-"
                    onclick="tryItOut('GETapi-v1-documents--document_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-documents--document_id-"
                    onclick="cancelTryOut('GETapi-v1-documents--document_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-documents--document_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/documents/{document_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-documents--document_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-documents--document_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>document_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="document_id"                data-endpoint="GETapi-v1-documents--document_id-"
               value="27"
               data-component="url">
    <br>
<p>The ID of the document. Example: <code>27</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-POSTapi-v1-auth-register">POST api/v1/auth/register</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-auth-register">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/auth/register" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"b\",
    \"phone_number\": \"ngzmiyvdljnikhwa\",
    \"email\": \"breitenberg.gilbert@example.com\",
    \"password\": \"kXaz&lt;m\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/auth/register"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "b",
    "phone_number": "ngzmiyvdljnikhwa",
    "email": "breitenberg.gilbert@example.com",
    "password": "kXaz&lt;m"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-auth-register">
</span>
<span id="execution-results-POSTapi-v1-auth-register" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-auth-register"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-auth-register"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-auth-register" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-auth-register">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-auth-register" data-method="POST"
      data-path="api/v1/auth/register"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-auth-register', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-auth-register"
                    onclick="tryItOut('POSTapi-v1-auth-register');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-auth-register"
                    onclick="cancelTryOut('POSTapi-v1-auth-register');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-auth-register"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/auth/register</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-auth-register"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-auth-register"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-v1-auth-register"
               value="b"
               data-component="body">
    <br>
<p>Must not be greater than 255 characters. Example: <code>b</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>phone_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="phone_number"                data-endpoint="POSTapi-v1-auth-register"
               value="ngzmiyvdljnikhwa"
               data-component="body">
    <br>
<p>Must not be greater than 20 characters. Example: <code>ngzmiyvdljnikhwa</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-v1-auth-register"
               value="breitenberg.gilbert@example.com"
               data-component="body">
    <br>
<p>Must be a valid email address. Must not be greater than 255 characters. Example: <code>breitenberg.gilbert@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-v1-auth-register"
               value="kXaz<m"
               data-component="body">
    <br>
<p>Must be at least 8 characters. Example: <code>kXaz&lt;m</code></p>
        </div>
        </form>

                    <h2 id="endpoints-POSTapi-v1-auth-login">Authenticate a user via their phone number and password.</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-auth-login">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/auth/login" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"phone_number\": \"architecto\",
    \"password\": \"|]|{+-\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/auth/login"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "phone_number": "architecto",
    "password": "|]|{+-"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-auth-login">
</span>
<span id="execution-results-POSTapi-v1-auth-login" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-auth-login"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-auth-login"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-auth-login" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-auth-login">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-auth-login" data-method="POST"
      data-path="api/v1/auth/login"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-auth-login', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-auth-login"
                    onclick="tryItOut('POSTapi-v1-auth-login');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-auth-login"
                    onclick="cancelTryOut('POSTapi-v1-auth-login');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-auth-login"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/auth/login</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-auth-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-auth-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>phone_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="phone_number"                data-endpoint="POSTapi-v1-auth-login"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-v1-auth-login"
               value="|]|{+-"
               data-component="body">
    <br>
<p>Example: <code>|]|{+-</code></p>
        </div>
        </form>

                    <h2 id="endpoints-POSTapi-v1-payments-webhook">POST api/v1/payments/webhook</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-payments-webhook">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/payments/webhook" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"order_id\": \"architecto\",
    \"status_code\": \"architecto\",
    \"gross_amount\": \"architecto\",
    \"signature_key\": \"architecto\",
    \"transaction_status\": \"architecto\",
    \"transaction_id\": \"architecto\",
    \"payment_type\": \"architecto\",
    \"fraud_status\": \"architecto\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/payments/webhook"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "order_id": "architecto",
    "status_code": "architecto",
    "gross_amount": "architecto",
    "signature_key": "architecto",
    "transaction_status": "architecto",
    "transaction_id": "architecto",
    "payment_type": "architecto",
    "fraud_status": "architecto"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-payments-webhook">
</span>
<span id="execution-results-POSTapi-v1-payments-webhook" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-payments-webhook"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-payments-webhook"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-payments-webhook" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-payments-webhook">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-payments-webhook" data-method="POST"
      data-path="api/v1/payments/webhook"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-payments-webhook', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-payments-webhook"
                    onclick="tryItOut('POSTapi-v1-payments-webhook');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-payments-webhook"
                    onclick="cancelTryOut('POSTapi-v1-payments-webhook');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-payments-webhook"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/payments/webhook</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-payments-webhook"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-payments-webhook"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>order_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="order_id"                data-endpoint="POSTapi-v1-payments-webhook"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status_code</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status_code"                data-endpoint="POSTapi-v1-payments-webhook"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>gross_amount</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="gross_amount"                data-endpoint="POSTapi-v1-payments-webhook"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>signature_key</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="signature_key"                data-endpoint="POSTapi-v1-payments-webhook"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>transaction_status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="transaction_status"                data-endpoint="POSTapi-v1-payments-webhook"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>transaction_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="transaction_id"                data-endpoint="POSTapi-v1-payments-webhook"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>payment_type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="payment_type"                data-endpoint="POSTapi-v1-payments-webhook"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>fraud_status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="fraud_status"                data-endpoint="POSTapi-v1-payments-webhook"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
        </form>

                    <h2 id="endpoints-GETapi-v1-auth-me">Get the authenticated user.</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-auth-me">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/auth/me" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/auth/me"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-auth-me">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
strict-transport-security: max-age=31536000; includeSubDomains
referrer-policy: no-referrer-when-downgrade
content-security-policy: default-src &#039;self&#039;; script-src &#039;self&#039; &#039;unsafe-inline&#039; &#039;unsafe-eval&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; style-src &#039;self&#039; &#039;unsafe-inline&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; img-src &#039;self&#039; data: https://*; font-src &#039;self&#039; data: https://*; connect-src &#039;self&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; frame-src &#039;self&#039; https://*
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-auth-me" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-auth-me"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-auth-me"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-auth-me" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-auth-me">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-auth-me" data-method="GET"
      data-path="api/v1/auth/me"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-auth-me', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-auth-me"
                    onclick="tryItOut('GETapi-v1-auth-me');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-auth-me"
                    onclick="cancelTryOut('GETapi-v1-auth-me');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-auth-me"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/auth/me</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-auth-me"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-auth-me"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-v1-user">Get the authenticated user.</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-user">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/user" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/user"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-user">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
strict-transport-security: max-age=31536000; includeSubDomains
referrer-policy: no-referrer-when-downgrade
content-security-policy: default-src &#039;self&#039;; script-src &#039;self&#039; &#039;unsafe-inline&#039; &#039;unsafe-eval&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; style-src &#039;self&#039; &#039;unsafe-inline&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; img-src &#039;self&#039; data: https://*; font-src &#039;self&#039; data: https://*; connect-src &#039;self&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; frame-src &#039;self&#039; https://*
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-user" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-user"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-user"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-user" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-user">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-user" data-method="GET"
      data-path="api/v1/user"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-user', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-user"
                    onclick="tryItOut('GETapi-v1-user');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-user"
                    onclick="cancelTryOut('GETapi-v1-user');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-user"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/user</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-user"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-user"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-POSTapi-v1-auth-logout">Revoke the current token.</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-auth-logout">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/auth/logout" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/auth/logout"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-auth-logout">
</span>
<span id="execution-results-POSTapi-v1-auth-logout" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-auth-logout"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-auth-logout"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-auth-logout" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-auth-logout">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-auth-logout" data-method="POST"
      data-path="api/v1/auth/logout"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-auth-logout', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-auth-logout"
                    onclick="tryItOut('POSTapi-v1-auth-logout');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-auth-logout"
                    onclick="cancelTryOut('POSTapi-v1-auth-logout');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-auth-logout"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/auth/logout</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-auth-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-auth-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-POSTapi-v1-webview-ticket">POST api/v1/webview-ticket</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-webview-ticket">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/webview-ticket" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/webview-ticket"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-webview-ticket">
</span>
<span id="execution-results-POSTapi-v1-webview-ticket" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-webview-ticket"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-webview-ticket"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-webview-ticket" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-webview-ticket">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-webview-ticket" data-method="POST"
      data-path="api/v1/webview-ticket"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-webview-ticket', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-webview-ticket"
                    onclick="tryItOut('POSTapi-v1-webview-ticket');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-webview-ticket"
                    onclick="cancelTryOut('POSTapi-v1-webview-ticket');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-webview-ticket"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/webview-ticket</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-webview-ticket"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-webview-ticket"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-v1-user-profile">GET api/v1/user/profile</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-user-profile">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/user/profile" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/user/profile"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-user-profile">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
strict-transport-security: max-age=31536000; includeSubDomains
referrer-policy: no-referrer-when-downgrade
content-security-policy: default-src &#039;self&#039;; script-src &#039;self&#039; &#039;unsafe-inline&#039; &#039;unsafe-eval&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; style-src &#039;self&#039; &#039;unsafe-inline&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; img-src &#039;self&#039; data: https://*; font-src &#039;self&#039; data: https://*; connect-src &#039;self&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; frame-src &#039;self&#039; https://*
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-user-profile" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-user-profile"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-user-profile"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-user-profile" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-user-profile">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-user-profile" data-method="GET"
      data-path="api/v1/user/profile"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-user-profile', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-user-profile"
                    onclick="tryItOut('GETapi-v1-user-profile');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-user-profile"
                    onclick="cancelTryOut('GETapi-v1-user-profile');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-user-profile"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/user/profile</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-user-profile"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-user-profile"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-PUTapi-v1-user-profile">PUT api/v1/user/profile</h2>

<p>
</p>



<span id="example-requests-PUTapi-v1-user-profile">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost/api/v1/user/profile" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"b\",
    \"email\": \"zbailey@example.net\",
    \"phone_number\": \"i\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/user/profile"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "b",
    "email": "zbailey@example.net",
    "phone_number": "i"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-v1-user-profile">
</span>
<span id="execution-results-PUTapi-v1-user-profile" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-v1-user-profile"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-v1-user-profile"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-v1-user-profile" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-v1-user-profile">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-v1-user-profile" data-method="PUT"
      data-path="api/v1/user/profile"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-v1-user-profile', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-v1-user-profile"
                    onclick="tryItOut('PUTapi-v1-user-profile');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-v1-user-profile"
                    onclick="cancelTryOut('PUTapi-v1-user-profile');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-v1-user-profile"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/v1/user/profile</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-v1-user-profile"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-v1-user-profile"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-v1-user-profile"
               value="b"
               data-component="body">
    <br>
<p>Must not be greater than 255 characters. Example: <code>b</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="PUTapi-v1-user-profile"
               value="zbailey@example.net"
               data-component="body">
    <br>
<p>Must be a valid email address. Must not be greater than 255 characters. Example: <code>zbailey@example.net</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>phone_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="phone_number"                data-endpoint="PUTapi-v1-user-profile"
               value="i"
               data-component="body">
    <br>
<p>Must not be greater than 255 characters. Example: <code>i</code></p>
        </div>
        </form>

                    <h2 id="endpoints-DELETEapi-v1-user-profile">DELETE api/v1/user/profile</h2>

<p>
</p>



<span id="example-requests-DELETEapi-v1-user-profile">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost/api/v1/user/profile" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/user/profile"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-v1-user-profile">
</span>
<span id="execution-results-DELETEapi-v1-user-profile" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-v1-user-profile"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-user-profile"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-user-profile" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-user-profile">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-v1-user-profile" data-method="DELETE"
      data-path="api/v1/user/profile"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-user-profile', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-user-profile"
                    onclick="tryItOut('DELETEapi-v1-user-profile');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-user-profile"
                    onclick="cancelTryOut('DELETEapi-v1-user-profile');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-user-profile"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/user/profile</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-user-profile"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-v1-user-profile"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-v1-organizations">GET api/v1/organizations</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-organizations">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/organizations" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/organizations"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-organizations">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
strict-transport-security: max-age=31536000; includeSubDomains
referrer-policy: no-referrer-when-downgrade
content-security-policy: default-src &#039;self&#039;; script-src &#039;self&#039; &#039;unsafe-inline&#039; &#039;unsafe-eval&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; style-src &#039;self&#039; &#039;unsafe-inline&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; img-src &#039;self&#039; data: https://*; font-src &#039;self&#039; data: https://*; connect-src &#039;self&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; frame-src &#039;self&#039; https://*
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-organizations" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-organizations"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-organizations"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-organizations" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-organizations">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-organizations" data-method="GET"
      data-path="api/v1/organizations"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-organizations', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-organizations"
                    onclick="tryItOut('GETapi-v1-organizations');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-organizations"
                    onclick="cancelTryOut('GETapi-v1-organizations');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-organizations"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/organizations</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-organizations"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-organizations"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-POSTapi-v1-organizations">POST api/v1/organizations</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-organizations">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/organizations" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"b\",
    \"logo\": \"architecto\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/organizations"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "b",
    "logo": "architecto"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-organizations">
</span>
<span id="execution-results-POSTapi-v1-organizations" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-organizations"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-organizations"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-organizations" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-organizations">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-organizations" data-method="POST"
      data-path="api/v1/organizations"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-organizations', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-organizations"
                    onclick="tryItOut('POSTapi-v1-organizations');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-organizations"
                    onclick="cancelTryOut('POSTapi-v1-organizations');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-organizations"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/organizations</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-organizations"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-organizations"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-v1-organizations"
               value="b"
               data-component="body">
    <br>
<p>Must not be greater than 255 characters. Example: <code>b</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>logo</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="logo"                data-endpoint="POSTapi-v1-organizations"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
        </form>

                    <h2 id="endpoints-PUTapi-v1-organizations--id-">PUT api/v1/organizations/{id}</h2>

<p>
</p>



<span id="example-requests-PUTapi-v1-organizations--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost/api/v1/organizations/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"b\",
    \"logo\": \"architecto\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/organizations/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "b",
    "logo": "architecto"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-v1-organizations--id-">
</span>
<span id="execution-results-PUTapi-v1-organizations--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-v1-organizations--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-v1-organizations--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-v1-organizations--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-v1-organizations--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-v1-organizations--id-" data-method="PUT"
      data-path="api/v1/organizations/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-v1-organizations--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-v1-organizations--id-"
                    onclick="tryItOut('PUTapi-v1-organizations--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-v1-organizations--id-"
                    onclick="cancelTryOut('PUTapi-v1-organizations--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-v1-organizations--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/v1/organizations/{id}</code></b>
        </p>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/v1/organizations/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-v1-organizations--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-v1-organizations--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PUTapi-v1-organizations--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the organization. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-v1-organizations--id-"
               value="b"
               data-component="body">
    <br>
<p>Must not be greater than 255 characters. Example: <code>b</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>logo</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="logo"                data-endpoint="PUTapi-v1-organizations--id-"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
        </form>

                    <h2 id="endpoints-DELETEapi-v1-organizations--id-">DELETE api/v1/organizations/{id}</h2>

<p>
</p>



<span id="example-requests-DELETEapi-v1-organizations--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost/api/v1/organizations/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/organizations/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-v1-organizations--id-">
</span>
<span id="execution-results-DELETEapi-v1-organizations--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-v1-organizations--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-organizations--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-organizations--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-organizations--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-v1-organizations--id-" data-method="DELETE"
      data-path="api/v1/organizations/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-organizations--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-organizations--id-"
                    onclick="tryItOut('DELETEapi-v1-organizations--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-organizations--id-"
                    onclick="cancelTryOut('DELETEapi-v1-organizations--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-organizations--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/organizations/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-organizations--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-v1-organizations--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="DELETEapi-v1-organizations--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the organization. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-v1-notifications">GET api/v1/notifications</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-notifications">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/notifications" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/notifications"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-notifications">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
strict-transport-security: max-age=31536000; includeSubDomains
referrer-policy: no-referrer-when-downgrade
content-security-policy: default-src &#039;self&#039;; script-src &#039;self&#039; &#039;unsafe-inline&#039; &#039;unsafe-eval&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; style-src &#039;self&#039; &#039;unsafe-inline&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; img-src &#039;self&#039; data: https://*; font-src &#039;self&#039; data: https://*; connect-src &#039;self&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; frame-src &#039;self&#039; https://*
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-notifications" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-notifications"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-notifications"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-notifications" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-notifications">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-notifications" data-method="GET"
      data-path="api/v1/notifications"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-notifications', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-notifications"
                    onclick="tryItOut('GETapi-v1-notifications');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-notifications"
                    onclick="cancelTryOut('GETapi-v1-notifications');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-notifications"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/notifications</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-notifications"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-notifications"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-v1-notifications-unread-count">GET api/v1/notifications/unread-count</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-notifications-unread-count">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/notifications/unread-count" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/notifications/unread-count"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-notifications-unread-count">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
strict-transport-security: max-age=31536000; includeSubDomains
referrer-policy: no-referrer-when-downgrade
content-security-policy: default-src &#039;self&#039;; script-src &#039;self&#039; &#039;unsafe-inline&#039; &#039;unsafe-eval&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; style-src &#039;self&#039; &#039;unsafe-inline&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; img-src &#039;self&#039; data: https://*; font-src &#039;self&#039; data: https://*; connect-src &#039;self&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; frame-src &#039;self&#039; https://*
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-notifications-unread-count" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-notifications-unread-count"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-notifications-unread-count"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-notifications-unread-count" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-notifications-unread-count">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-notifications-unread-count" data-method="GET"
      data-path="api/v1/notifications/unread-count"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-notifications-unread-count', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-notifications-unread-count"
                    onclick="tryItOut('GETapi-v1-notifications-unread-count');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-notifications-unread-count"
                    onclick="cancelTryOut('GETapi-v1-notifications-unread-count');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-notifications-unread-count"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/notifications/unread-count</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-notifications-unread-count"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-notifications-unread-count"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-POSTapi-v1-notifications-mark-all-read">POST api/v1/notifications/mark-all-read</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-notifications-mark-all-read">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/notifications/mark-all-read" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/notifications/mark-all-read"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-notifications-mark-all-read">
</span>
<span id="execution-results-POSTapi-v1-notifications-mark-all-read" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-notifications-mark-all-read"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-notifications-mark-all-read"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-notifications-mark-all-read" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-notifications-mark-all-read">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-notifications-mark-all-read" data-method="POST"
      data-path="api/v1/notifications/mark-all-read"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-notifications-mark-all-read', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-notifications-mark-all-read"
                    onclick="tryItOut('POSTapi-v1-notifications-mark-all-read');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-notifications-mark-all-read"
                    onclick="cancelTryOut('POSTapi-v1-notifications-mark-all-read');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-notifications-mark-all-read"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/notifications/mark-all-read</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-notifications-mark-all-read"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-notifications-mark-all-read"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-POSTapi-v1-notifications--id--mark-read">POST api/v1/notifications/{id}/mark-read</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-notifications--id--mark-read">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/notifications/architecto/mark-read" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/notifications/architecto/mark-read"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-notifications--id--mark-read">
</span>
<span id="execution-results-POSTapi-v1-notifications--id--mark-read" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-notifications--id--mark-read"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-notifications--id--mark-read"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-notifications--id--mark-read" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-notifications--id--mark-read">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-notifications--id--mark-read" data-method="POST"
      data-path="api/v1/notifications/{id}/mark-read"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-notifications--id--mark-read', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-notifications--id--mark-read"
                    onclick="tryItOut('POSTapi-v1-notifications--id--mark-read');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-notifications--id--mark-read"
                    onclick="cancelTryOut('POSTapi-v1-notifications--id--mark-read');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-notifications--id--mark-read"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/notifications/{id}/mark-read</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-notifications--id--mark-read"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-notifications--id--mark-read"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="POSTapi-v1-notifications--id--mark-read"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the notification. Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-v1-registrations">GET api/v1/registrations</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-registrations">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/registrations" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/registrations"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-registrations">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
strict-transport-security: max-age=31536000; includeSubDomains
referrer-policy: no-referrer-when-downgrade
content-security-policy: default-src &#039;self&#039;; script-src &#039;self&#039; &#039;unsafe-inline&#039; &#039;unsafe-eval&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; style-src &#039;self&#039; &#039;unsafe-inline&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; img-src &#039;self&#039; data: https://*; font-src &#039;self&#039; data: https://*; connect-src &#039;self&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; frame-src &#039;self&#039; https://*
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-registrations" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-registrations"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-registrations"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-registrations" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-registrations">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-registrations" data-method="GET"
      data-path="api/v1/registrations"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-registrations', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-registrations"
                    onclick="tryItOut('GETapi-v1-registrations');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-registrations"
                    onclick="cancelTryOut('GETapi-v1-registrations');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-registrations"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/registrations</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-registrations"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-registrations"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-POSTapi-v1-registrations">POST api/v1/registrations</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-registrations">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/registrations" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"event_id\": \"architecto\",
    \"packages\": [
        {
            \"package_id\": \"architecto\",
            \"count\": 22
        }
    ]
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/registrations"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "event_id": "architecto",
    "packages": [
        {
            "package_id": "architecto",
            "count": 22
        }
    ]
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-registrations">
</span>
<span id="execution-results-POSTapi-v1-registrations" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-registrations"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-registrations"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-registrations" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-registrations">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-registrations" data-method="POST"
      data-path="api/v1/registrations"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-registrations', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-registrations"
                    onclick="tryItOut('POSTapi-v1-registrations');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-registrations"
                    onclick="cancelTryOut('POSTapi-v1-registrations');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-registrations"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/registrations</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-registrations"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-registrations"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>event_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="event_id"                data-endpoint="POSTapi-v1-registrations"
               value="architecto"
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the events table. Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>organization_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="organization_id"                data-endpoint="POSTapi-v1-registrations"
               value=""
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the organizations table.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>packages</code></b>&nbsp;&nbsp;
<small>object[]</small>&nbsp;
 &nbsp;
 &nbsp;
<br>
<p>Must have at least 1 items.</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>package_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="packages.0.package_id"                data-endpoint="POSTapi-v1-registrations"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>count</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="packages.0.count"                data-endpoint="POSTapi-v1-registrations"
               value="22"
               data-component="body">
    <br>
<p>Must be at least 1. Example: <code>22</code></p>
                    </div>
                                    </details>
        </div>
        </form>

                    <h2 id="endpoints-GETapi-v1-registrations--id-">GET api/v1/registrations/{id}</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-registrations--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/registrations/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/registrations/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-registrations--id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
strict-transport-security: max-age=31536000; includeSubDomains
referrer-policy: no-referrer-when-downgrade
content-security-policy: default-src &#039;self&#039;; script-src &#039;self&#039; &#039;unsafe-inline&#039; &#039;unsafe-eval&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; style-src &#039;self&#039; &#039;unsafe-inline&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; img-src &#039;self&#039; data: https://*; font-src &#039;self&#039; data: https://*; connect-src &#039;self&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; frame-src &#039;self&#039; https://*
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-registrations--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-registrations--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-registrations--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-registrations--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-registrations--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-registrations--id-" data-method="GET"
      data-path="api/v1/registrations/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-registrations--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-registrations--id-"
                    onclick="tryItOut('GETapi-v1-registrations--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-registrations--id-"
                    onclick="cancelTryOut('GETapi-v1-registrations--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-registrations--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/registrations/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-registrations--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-registrations--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-v1-registrations--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the registration. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-PUTapi-v1-registrations--id-">PUT api/v1/registrations/{id}</h2>

<p>
</p>



<span id="example-requests-PUTapi-v1-registrations--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost/api/v1/registrations/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/registrations/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "PUT",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-v1-registrations--id-">
</span>
<span id="execution-results-PUTapi-v1-registrations--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-v1-registrations--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-v1-registrations--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-v1-registrations--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-v1-registrations--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-v1-registrations--id-" data-method="PUT"
      data-path="api/v1/registrations/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-v1-registrations--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-v1-registrations--id-"
                    onclick="tryItOut('PUTapi-v1-registrations--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-v1-registrations--id-"
                    onclick="cancelTryOut('PUTapi-v1-registrations--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-v1-registrations--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/v1/registrations/{id}</code></b>
        </p>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/v1/registrations/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-v1-registrations--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-v1-registrations--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PUTapi-v1-registrations--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the registration. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>organization_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="organization_id"                data-endpoint="PUTapi-v1-registrations--id-"
               value=""
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the organizations table.</p>
        </div>
        </form>

                    <h2 id="endpoints-DELETEapi-v1-registrations--id-">DELETE api/v1/registrations/{id}</h2>

<p>
</p>



<span id="example-requests-DELETEapi-v1-registrations--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost/api/v1/registrations/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/registrations/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-v1-registrations--id-">
</span>
<span id="execution-results-DELETEapi-v1-registrations--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-v1-registrations--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-registrations--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-registrations--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-registrations--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-v1-registrations--id-" data-method="DELETE"
      data-path="api/v1/registrations/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-registrations--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-registrations--id-"
                    onclick="tryItOut('DELETEapi-v1-registrations--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-registrations--id-"
                    onclick="cancelTryOut('DELETEapi-v1-registrations--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-registrations--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/registrations/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-registrations--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-v1-registrations--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="DELETEapi-v1-registrations--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the registration. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-v1-invoices">GET api/v1/invoices</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-invoices">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/invoices" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/invoices"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-invoices">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
strict-transport-security: max-age=31536000; includeSubDomains
referrer-policy: no-referrer-when-downgrade
content-security-policy: default-src &#039;self&#039;; script-src &#039;self&#039; &#039;unsafe-inline&#039; &#039;unsafe-eval&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; style-src &#039;self&#039; &#039;unsafe-inline&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; img-src &#039;self&#039; data: https://*; font-src &#039;self&#039; data: https://*; connect-src &#039;self&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; frame-src &#039;self&#039; https://*
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-invoices" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-invoices"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-invoices"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-invoices" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-invoices">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-invoices" data-method="GET"
      data-path="api/v1/invoices"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-invoices', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-invoices"
                    onclick="tryItOut('GETapi-v1-invoices');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-invoices"
                    onclick="cancelTryOut('GETapi-v1-invoices');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-invoices"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/invoices</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-invoices"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-invoices"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-v1-invoices--invoice_id-">GET api/v1/invoices/{invoice_id}</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-invoices--invoice_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/invoices/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/invoices/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-invoices--invoice_id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
strict-transport-security: max-age=31536000; includeSubDomains
referrer-policy: no-referrer-when-downgrade
content-security-policy: default-src &#039;self&#039;; script-src &#039;self&#039; &#039;unsafe-inline&#039; &#039;unsafe-eval&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; style-src &#039;self&#039; &#039;unsafe-inline&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; img-src &#039;self&#039; data: https://*; font-src &#039;self&#039; data: https://*; connect-src &#039;self&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; frame-src &#039;self&#039; https://*
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-invoices--invoice_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-invoices--invoice_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-invoices--invoice_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-invoices--invoice_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-invoices--invoice_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-invoices--invoice_id-" data-method="GET"
      data-path="api/v1/invoices/{invoice_id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-invoices--invoice_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-invoices--invoice_id-"
                    onclick="tryItOut('GETapi-v1-invoices--invoice_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-invoices--invoice_id-"
                    onclick="cancelTryOut('GETapi-v1-invoices--invoice_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-invoices--invoice_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/invoices/{invoice_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-invoices--invoice_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-invoices--invoice_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>invoice_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="invoice_id"                data-endpoint="GETapi-v1-invoices--invoice_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the invoice. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-v1-invoices--invoice_id--download">GET api/v1/invoices/{invoice_id}/download</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-invoices--invoice_id--download">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/invoices/1/download" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/invoices/1/download"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-invoices--invoice_id--download">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
strict-transport-security: max-age=31536000; includeSubDomains
referrer-policy: no-referrer-when-downgrade
content-security-policy: default-src &#039;self&#039;; script-src &#039;self&#039; &#039;unsafe-inline&#039; &#039;unsafe-eval&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; style-src &#039;self&#039; &#039;unsafe-inline&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; img-src &#039;self&#039; data: https://*; font-src &#039;self&#039; data: https://*; connect-src &#039;self&#039; https://* http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*; frame-src &#039;self&#039; https://*
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-invoices--invoice_id--download" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-invoices--invoice_id--download"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-invoices--invoice_id--download"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-invoices--invoice_id--download" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-invoices--invoice_id--download">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-invoices--invoice_id--download" data-method="GET"
      data-path="api/v1/invoices/{invoice_id}/download"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-invoices--invoice_id--download', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-invoices--invoice_id--download"
                    onclick="tryItOut('GETapi-v1-invoices--invoice_id--download');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-invoices--invoice_id--download"
                    onclick="cancelTryOut('GETapi-v1-invoices--invoice_id--download');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-invoices--invoice_id--download"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/invoices/{invoice_id}/download</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-invoices--invoice_id--download"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-invoices--invoice_id--download"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>invoice_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="invoice_id"                data-endpoint="GETapi-v1-invoices--invoice_id--download"
               value="1"
               data-component="url">
    <br>
<p>The ID of the invoice. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-POSTapi-v1-payments-charge">POST api/v1/payments/charge</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-payments-charge">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/payments/charge" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"invoice_id\": 16
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/payments/charge"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "invoice_id": 16
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-payments-charge">
</span>
<span id="execution-results-POSTapi-v1-payments-charge" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-payments-charge"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-payments-charge"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-payments-charge" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-payments-charge">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-payments-charge" data-method="POST"
      data-path="api/v1/payments/charge"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-payments-charge', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-payments-charge"
                    onclick="tryItOut('POSTapi-v1-payments-charge');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-payments-charge"
                    onclick="cancelTryOut('POSTapi-v1-payments-charge');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-payments-charge"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/payments/charge</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-payments-charge"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-payments-charge"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>invoice_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="invoice_id"                data-endpoint="POSTapi-v1-payments-charge"
               value="16"
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the invoices table. Example: <code>16</code></p>
        </div>
        </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                            </div>
            </div>
</div>
</body>
</html>

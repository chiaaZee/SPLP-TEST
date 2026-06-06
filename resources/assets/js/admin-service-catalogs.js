/**
 * Page Admin Service Catalog Detail (Standard List View)
 * Updated for Livewire Hybrid Mode:
 * - Removed: Create/Edit/Delete Endpoint (Moved to Livewire)
 * - Kept: Test Endpoint, Documentation, Copy Utils
 */

'use strict';

$(function () {
    var baseUrl = "{{ url('/') }}/"; // Ensure baseUrl is defined if used, or rely on global defined in layout

    // Test Endpoint
    $(document).on('click', '.test-endpoint', function () {
        var id = $(this).data('id');
        var method = $(this).data('method');
        // Check if baseUrl is defined globally or derive it
        // layoutMaster usually defines standard vars, but if not, let's be safe.
        // Actually, the original script used `baseUrl` variable which might have been defined globally in layout.
        // Let's assume `baseUrl` is global or available.
        // If not, we might need: var baseUrl = $('meta[name="base-url"]').attr('content') || '/';

        $('#modalTestApi').modal('show');
        $('#test_status').text('Loading...').attr('class', 'badge bg-secondary');
        $('#test_method').text(method);
        $('#test_body').text('Sending request...');
        $('#test_duration').text('-');

        $.ajax({
            url: window.baseUrl + 'admin/service-catalogs/endpoint/' + id + '/test',
            type: 'GET',
            success: function (res) {
                var statusClass = res.success ? 'bg-success' : 'bg-danger';
                $('#test_status').attr('class', 'badge ' + statusClass).text(res.status);
                $('#test_duration').text(res.duration);

                var body = res.body;
                if (typeof body === 'object') body = JSON.stringify(body, null, 2);
                $('#test_body').text(body);
            },
            error: function (err) {
                $('#test_status').attr('class', 'badge bg-danger').text('Error');
                $('#test_duration').text('0 ms');
                $('#test_body').text(JSON.stringify(err, null, 2));
            }
        });
    });

    $(document).on('click', '.view-docs', function () {
        var method = $(this).data('method');
        var url = $(this).data('url');

        // Update Side Panel
        $('#doc_url_input').val(url);
        $('#doc_method_badge').text(method);

        var body = $(this).data('body');
        if (typeof body === 'object') {
            body = JSON.stringify(body, null, 2);
        }
        var bodyStr = body ? String(body).replace(/'/g, "\\'") : ""; // Escape single quotes for some contexts if needed
        var hasBody = (method === 'POST' || method === 'PUT') && body;

        // cURL
        var curl = `curl -X ${method} "${url}" \\
   -H "Authorization: Bearer YOUR_API_TOKEN" \\
   -H "Content-Type: application/json"`;
        if (hasBody) {
            curl += ` \\\n   -d '${body}'`;
        }

        // PHP
        var php = `use Illuminate\\Support\\Facades\\Http;

$response = Http::withToken('YOUR_API_TOKEN')`;
        if (hasBody) {
            php += `\n  ->withBody('${bodyStr}', 'application/json')`;
        }
        php += `\n  ->${method.toLowerCase()}('${url}');

return $response->json();`;

        // NodeJS (Fetch)
        var js = `fetch("${url}", {
  method: "${method}",
  headers: {
      "Authorization": "Bearer YOUR_API_TOKEN",
      "Content-Type": "application/json"
  }`;
        if (hasBody) {
            js += `,\n  body: JSON.stringify(${body})`; // Assuming body is valid JSON string, or just raw
        }
        js += `
})
.then(response => response.json())
.then(data => { /* Handle data */ })
.catch(error => console.error(error));`;

        // Android (Kotlin)
        var android = `val client = OkHttpClient()

val mediaType = "application/json; charset=utf-8".toMediaType()
val body = ${hasBody ? `"${bodyStr}".toRequestBody(mediaType)` : 'null'}

val request = Request.Builder()
  .url("${url}")
  .addHeader("Authorization", "Bearer YOUR_API_TOKEN")
  .method("${method}", body)
  .build()

val response = client.newCall(request).execute()
println(response.body?.string())`;

        $('#doc_code_curl').text(curl);
        $('#doc_code_php').text(php);
        $('#doc_code_js').text(js);
        $('#doc_code_android').text(android);

        // Reset Tabs to first one
        $('.nav-code-tabs button:first').tab('show');

        // Show Modal
        $('#modalDocs').modal('show');
    });

    // Copy URL
    $('#btnCopyUrl').on('click', function () {
        var copyText = document.getElementById("doc_url_input");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);

        var btn = $(this);
        var originalIcon = btn.html();
        btn.html('<i class="ti ti-check"></i>');
        setTimeout(function () { btn.html(originalIcon); }, 1500);
    });

    // Copy Code (Active Tab)
    $('#btnCopyCode').on('click', function () {
        var activeTabId = $('.nav-code-tabs .active').attr('data-bs-target');
        var codeContent = $(activeTabId + ' pre').text();

        navigator.clipboard.writeText(codeContent);

        var btn = $(this);
        var originalIcon = btn.html();
        btn.html('<i class="ti ti-check text-success"></i>');
        setTimeout(function () { btn.html(originalIcon); }, 1500);
    });

    // Fix Z-Index for Dropdowns in Animated Lists
    $(document).on('show.bs.dropdown', '.dropdown', function () {
        var item = $(this).closest('.animate__animated');
        item.css({ 'z-index': 1000, 'position': 'relative' });
        $(this).closest('.card').css('overflow', 'visible');
    });
    $(document).on('hide.bs.dropdown', '.dropdown', function () {
        var item = $(this).closest('.animate__animated');
        item.css({ 'z-index': '', 'position': '' });
    });
});

<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ domain }}</loc>
        <lastmod>{{ date('Y-m-d\TH:i:s+00:00') }}</lastmod>
        <changefreq>hourly</changefreq>
        <priority>1.0</priority>
    </url>
{% for post in posts %}
    <url>
        <loc>{{ domain }}{{ post.route }}</loc>
{% set postDate = post.modifiedAt ? post.modifiedAt : post.createdAt %}
        <lastmod>{{ date('Y-m-d\TH:i:s+00:00', strtotime(postDate)) }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
{% endfor %}
{% for category in categories %}
    <url>
        <loc>{{ domain }}{{ category.route }}</loc>
        <lastmod>{{ date('Y-m-d\TH:i:s+00:00') }}</lastmod>
        <changefreq>hourly</changefreq>
        <priority>0.9</priority>
    </url>
{% endfor %}
</urlset>
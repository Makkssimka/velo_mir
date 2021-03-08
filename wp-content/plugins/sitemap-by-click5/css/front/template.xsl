<?xml version="1.0" encoding="UTF-8"?>
	<xsl:stylesheet version="2.0"
    xmlns:ns="http://www.sitemaps.org/schemas/sitemap/0.9"
		xmlns:html="http://www.w3.org/TR/REC-html40"
		xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
		xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
		xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
	<xsl:template match="/">
		<html xmlns="http://www.w3.org/1999/xhtml">
      <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></meta>
      <title>XML Sitemap</title>
      </head>
      <body>
      <style type="text/css">
                        body {
                          font-family: monospace;
                          font-size: 13px;
                          color: black;
                        }
                        h1 {
                          color: #777;
                        }
                        table {
                          border: none;
                          border-collapse: collapse;
                          width: 100%;
                        }
                        #sitemap tr:nth-child(odd) td {
                          background-color: #DBF2FB !important;
                          color: black;
                        }
                        #content {
                          margin: 0 auto;
                          width: 768px;
                        }
                        a {
                          color: inherit;
                          text-decoration: none;
                        }
                        a:hover {
                          text-decoration: underline;
                        }
                        #heading a {
                          color: #37B5E9;
                        }
                        th {
                          padding-right:3rem;
                          text-align:left;
                          font-size:11px;
                        }
                        td {
                          font-size:10px;
                        }
      </style>
      <div id="content">
      <div id="heading">
        <h1>XML Sitemap</h1>
        <xsl:for-each select="urlset|ns:urlset">
          <p>This sitemap file contains <xsl:value-of select="count(../url|ns:url)"/> URLs.</p>
        </xsl:for-each>
      </div>
      <table id="sitemap"> 
      <tbody>
      <tr>
          <th>URL</th>
          <th>Last Modified</th>
      </tr>

      <xsl:for-each select="urlset|ns:urlset">
        <xsl:for-each select="../url|ns:url">
          <tr>
            <td>
            <a><xsl:attribute name="href">
                    <xsl:value-of select="loc|ns:loc"/></xsl:attribute>
                    <xsl:value-of select="loc|ns:loc"/>
            </a>
          </td>
          <td><xsl:value-of select="lastmod|ns:lastmod" /></td>
          </tr>
        </xsl:for-each>
      </xsl:for-each>
      </tbody>
      </table>
      </div>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>
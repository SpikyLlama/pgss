<html>
  <body>
    <p>finishing login... please hang tight!</p>
    <script>
      async function finish_login() {
      const urlParams = new URLSearchParams(window.location.search);
      const code = urlParams.get("code");

      const form = new URLSearchParams({
        grant_type: "authorization_code",
        code: code,
        redirect_uri: "https://spikyllama.net/pgss/finish_login.php"
      });
      const credentials = btoa(`MzRgMlET7I_0RUGyxfxEgA:<?php echo "CLIENT_SECRET" ?>`);
      const res = await fetch('https://www.reddit.com/api/v1/access_token', { 
        method: "POST",
        headers: {
          Authorization: `Basic ${credentials}`
        },
        body: form
      });
      const result = await res.json();
      localStorage.setItem("access_token", result.access_token);
      localStorage.setItem("refresh_token", result.refresh_token);
      localStorage.setItem("last_refreshed", Date.now() / 1000);
      window.location.replace("index.php");
    };
    finish_login();
    </script>
  </body>
</html>
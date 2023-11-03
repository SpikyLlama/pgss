<html>
  <body>
    <p>finishing login... please hang tight!</p>
    <script>
      async function finish_login() {
      let secrets;
      let client_id, client_secret;
      // await import("./creds.js")
      //   .then(response => {
      //       secrets = {...response.creds};
      //       client_id = secrets.client_id;
      //       client_secret = secrets.client_secret;
      //       return client_id, client_secret;
      //   });
      const urlParams = new URLSearchParams(window.location.search);
      const code = urlParams.get("code");

      const form = new URLSearchParams({
        grant_type: "authorization_code",
        code: code,
        redirect_uri: "http://127.0.0.1:5500/finish_login.html"
      });
      const credentials = btoa(`${<?php echo "xn5POgMEsX6rXpfnNHdGBg" ?>}:${<?php echo "i4atnwSG45m65BezcoEceECP7o85VQ" ?>}`);
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
      window.location.replace("index.html");
    };
    finish_login();
    </script>
  </body>
</html>
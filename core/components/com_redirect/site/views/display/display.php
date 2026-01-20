  <?php
    $url = $this->url;
    $seconds = $this->time;
    $domain = $this->domain;
    ?>
  <style>
      .redirect {
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          padding: 10em;
      }

      .redirect .container {
          background-color: white;
          padding: 2rem 3rem;
          border-radius: 12px;
          box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
          max-width: 500px;
          justify-content: center;
          display: flex;
          flex-direction: column;
          align-items: center;

      }

      .redirect h1 {
          font-size: 2rem;
          margin-bottom: 0.5rem;
          color: #00629b;
      }

      .redirect p {
          font-size: 1.2rem;
          margin-top: 0;
      }

      .redirect .counter {
          font-size: 2rem;
          font-weight: bold;
          margin-top: 1rem;
          color: #00629b;
      }

      .redirect .footer {
          margin-top: 2rem;
          font-size: 0.9rem;
          color: #666;
      }

      .redirect a {
          color: #00629b;
          text-decoration: none;
      }

      .redirect a:hover {
          text-decoration: underline;
      }
  </style>
  <div class="redirect">
      <div class="container">
          <h1>Redirecting Soon...</h1>
          <p>You will be redirected to
              <a href="<?php echo $url ?>" rel="noreferrer nofollow noopener"><?php echo $domain ?></a> in</p>
          <div class="counter" id="countdown"><?php echo $seconds ?></div>
          <div class="footer">If you're not redirected,
              <a href="<?php echo $url ?>" rel="noreferrer nofollow noopener">click here</a>.</div>
      </div>

      <script>
          let seconds = <?php echo $seconds ?>;
          const countdownEl = document.getElementById("countdown");

          const interval = setInterval(() => {
              seconds--;
              countdownEl.textContent = seconds;
              if (seconds <= 0) {
                  clearInterval(interval);
                  const a = document.createElement("a");
                  a.href = "<?php echo $url ?>";
                  a.rel = "noreferrer nofollow noopener";
                  document.body.appendChild(a);
                  a.click();
              }
          }, 1000);
      </script>
  </div>
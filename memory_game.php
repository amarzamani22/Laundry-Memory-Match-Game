<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Memory Match - Laundry Game</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #e0f7fa, #e0f2f1);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .game-container {
      background: #fff;
      border-radius: 16px;
      padding: 30px 25px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.15);
      width: 90%;
      max-width: 700px;
      text-align: center;
      position: relative;
    }
    .game-title {
      font-size: 28px;
      font-weight: 600;
      margin-bottom: 20px;
      color: #2B5D61;
    }
    .timer {
      font-size: 18px;
      color: #B22222;
      margin-bottom: 20px;
    }
    .grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 15px;
    }
    .card {
      background-color: #f9f9f9;
      border-radius: 10px;
      height: 100px;
      cursor: pointer;
      perspective: 1000px;
      position: relative;
    }
    .card-inner {
      position: absolute;
      width: 100%;
      height: 100%;
      transition: transform 0.6s;
      transform-style: preserve-3d;
    }
    .card.flip .card-inner {
      transform: rotateY(180deg);
    }
    .card-front, .card-back {
      position: absolute;
      width: 100%;
      height: 100%;
      backface-visibility: hidden;
      border-radius: 10px;
    }
    .card-front {
      background-color: #ccc;
    }
    .card-back {
      background-color: #fff;
      transform: rotateY(180deg);
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .card-back img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: 10px;
      padding: 8px;
    }
    .message {
      margin-top: 20px;
      font-size: 18px;
      font-weight: 600;
      color: green;
    }
    .btn {
      margin-top: 25px;
      padding: 10px 20px;
      background-color: #2B5D61;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      display: none;
    }
    .btn:hover {
      background-color: #244c4c;
    }
    @media (max-width: 600px) {
      .grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }
  </style>
</head>
<body>

<div class="game-container">
  <div class="game-title">🧹 Laundry Memory Match</div>
  <div class="timer">Time Left: <span id="time">60</span>s</div>
  <div class="grid" id="game-board"></div>
  <div class="message" id="message"></div>
  <button class="btn" onclick="window.location.href='games.php'" id="backBtn">Back to Games</button>
</div>

<script>
  const images = [
    'images/item1.jpg', 'images/item1.jpg',
    'images/item2.jpg', 'images/item2.jpg',
    'images/item3.jpg', 'images/item3.jpg',
    'images/item4.jpg', 'images/item4.jpg',
    'images/item5.jpg', 'images/item5.jpg',
    'images/item6.jpg', 'images/item6.jpg',
  ];

  let cards = [];
  let firstCard = null;
  let secondCard = null;
  let lockBoard = false;
  let matched = 0;
  let timer;
  let timeLeft = 60;
  const board = document.getElementById("game-board");
  const message = document.getElementById("message");
  const timeDisplay = document.getElementById("time");
  const backBtn = document.getElementById("backBtn");

  function shuffle(array) {
    return array.sort(() => 0.5 - Math.random());
  }

  function createBoard() {
    const shuffled = shuffle(images.slice());
    shuffled.forEach(src => {
      const card = document.createElement("div");
      card.classList.add("card");
      card.innerHTML = `
        <div class="card-inner">
          <div class="card-front"></div>
          <div class="card-back"><img src="${src}" alt="item" /></div>
        </div>
      `;
      board.appendChild(card);
      card.addEventListener("click", () => flipCard(card));
    });
  }

  function flipCard(card) {
    if (lockBoard || card.classList.contains("flip")) return;
    card.classList.add("flip");
    if (!firstCard) {
      firstCard = card;
    } else {
      secondCard = card;
      checkMatch();
    }
  }

  function checkMatch() {
    const img1 = firstCard.querySelector("img").src;
    const img2 = secondCard.querySelector("img").src;
    if (img1 === img2) {
      matched += 2;
      reset();
      if (matched === images.length) {
        clearInterval(timer);
        message.textContent = "✨ You matched all items!";
        backBtn.style.display = "inline-block";
        logPlayAndAward();
      }
    } else {
      lockBoard = true;
      setTimeout(() => {
        firstCard.classList.remove("flip");
        secondCard.classList.remove("flip");
        reset();
      }, 1000);
    }
  }

  function reset() {
    [firstCard, secondCard, lockBoard] = [null, null, false];
  }

  function startTimer() {
    timer = setInterval(() => {
      timeLeft--;
      timeDisplay.textContent = timeLeft;
      if (timeLeft <= 0) {
        clearInterval(timer);
        lockBoard = true;
        message.textContent = "⏱️ Time's up! Try again tomorrow!";
        backBtn.style.display = "inline-block";
      }
    }, 1000);
  }

  function logPlayAndAward() {
    fetch("log_game_play.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ game: "memory" })
    }).then(res => res.json()).then(data => {
      if (data.success) {
        fetch("award_points.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ points: 10 })
        });
      } else if (data.error) {
        console.warn(data.error);
      }
    });
  }

  createBoard();
  startTimer();
</script>

</body>
</html>

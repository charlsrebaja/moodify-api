// Google Sign-in handler
function handleCredentialResponse(response) {
  // Decode the JWT token
  const responsePayload = decodeJwtResponse(response.credential);

  // You can access user information from responsePayload
  console.log("ID: " + responsePayload.sub);
  console.log("Full Name: " + responsePayload.name);
  console.log("Given Name: " + responsePayload.given_name);
  console.log("Family Name: " + responsePayload.family_name);
  console.log("Image URL: " + responsePayload.picture);
  console.log("Email: " + responsePayload.email);

  // Here you can make an API call to your backend to handle the sign-in
  // and store the user information in your database
}

// Function to decode the JWT token
function decodeJwtResponse(token) {
  const base64Url = token.split(".")[1];
  const base64 = base64Url.replace(/-/g, "+").replace(/_/g, "/");
  const jsonPayload = decodeURIComponent(
    atob(base64)
      .split("")
      .map(function (c) {
        return "%" + ("00" + c.charCodeAt(0).toString(16)).slice(-2);
      })
      .join("")
  );
  return JSON.parse(jsonPayload);
}

// Dark mode toggle functionality
function initDarkMode() {
  const darkModeToggle = document.getElementById("darkModeToggle");

  darkModeToggle.addEventListener("click", () => {
    document.documentElement.classList.toggle("dark");

    // Save preference
    if (document.documentElement.classList.contains("dark")) {
      localStorage.theme = "dark";
    } else {
      localStorage.theme = "light";
    }
  });
}

// Function to render playlists
function renderPlaylists(playlists) {
  const playlistsContainer = document.getElementById("playlists");
  playlistsContainer.innerHTML = playlists
    .map(
      (playlist) => `
              <div class="playlist-card">
                  <img 
                      src="${playlist.image}" 
                      alt="${playlist.name}" 
                      class="playlist-image"
                      onerror="this.src='/spotify/public/assets/img/default-playlist.png'"
                  >
                  <h3 class="playlist-title">${playlist.name}</h3>
                  <p class="playlist-description">${
                    playlist.description || ""
                  }</p>
                  <p class="playlist-meta">By ${
                    playlist.owner
                  } • ${formatFollowers(playlist.followers)} followers</p>
                  <a 
                      href="${playlist.external_url}" 
                      target="_blank" 
                      rel="noopener noreferrer" 
                      class="spotify-button"
                  >
                      Open in Spotify
                      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                          <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                      </svg>
                  </a>
              </div>
          `
    )
    .join("");
}

// Function to load random playlists
async function loadRandomPlaylists() {
  const loadingSection = document.getElementById("loading");
  const errorSection = document.getElementById("error");
  const errorMessage = document.getElementById("error-message");
  const emptySection = document.getElementById("empty");
  const playlistsContainer = document.getElementById("playlists");

  // Show loading state
  playlistsContainer.innerHTML = "";
  loadingSection.classList.remove("hidden");
  errorSection.classList.add("hidden");
  emptySection.classList.add("hidden");

  try {
    const response = await fetch(
      `/spotify/public/api/playlists.php?random=true`
    );
    const data = await response.json();

    if (!response.ok)
      throw new Error(data.message || "Failed to fetch playlists");

    // Hide loading state
    loadingSection.classList.add("hidden");

    if (data.data.length === 0) {
      emptySection.classList.remove("hidden");
      return;
    }

    renderPlaylists(data.data);
  } catch (error) {
    loadingSection.classList.add("hidden");
    errorSection.classList.remove("hidden");
    errorMessage.textContent = error.message;
  }
}

// Mobile menu functionality
function initMobileMenu() {
  const mobileMenuBtn = document.getElementById("mobileMenuBtn");
  const mobileMenu = document.getElementById("mobileMenu");
  const menuIcon = document.getElementById("menuIcon");
  const closeIcon = document.getElementById("closeIcon");
  const mobileDarkModeToggle = document.getElementById("mobileDarkModeToggle");

  if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener("click", () => {
      const isExpanded = mobileMenuBtn.getAttribute("aria-expanded") === "true";

      mobileMenuBtn.setAttribute("aria-expanded", !isExpanded);
      mobileMenu.classList.toggle("hidden");
      menuIcon.classList.toggle("hidden");
      closeIcon.classList.toggle("hidden");

      if (!isExpanded) {
        // Opening the menu
        mobileMenu.style.maxHeight = mobileMenu.scrollHeight + "px";
        mobileMenu.style.opacity = "1";
      } else {
        // Closing the menu
        mobileMenu.style.maxHeight = "0";
        mobileMenu.style.opacity = "0";
      }
    });
  }

  // Sync mobile dark mode toggle with desktop
  if (mobileDarkModeToggle) {
    mobileDarkModeToggle.addEventListener("click", () => {
      document.documentElement.classList.toggle("dark");
      if (document.documentElement.classList.contains("dark")) {
        localStorage.theme = "dark";
      } else {
        localStorage.theme = "light";
      }
    });
  }
}

document.addEventListener("DOMContentLoaded", () => {
  initDarkMode();
  initMobileMenu();
  const moodButtons = document.querySelectorAll(".mood-btn");
  const playlistsContainer = document.getElementById("playlists");
  const loadingSection = document.getElementById("loading");
  const errorSection = document.getElementById("error");
  const errorMessage = document.getElementById("error-message");
  const emptySection = document.getElementById("empty");

  // Load random playlists on page load
  loadRandomPlaylists();

  let currentMood = null;

  moodButtons.forEach((button) => {
    button.addEventListener("click", async () => {
      const mood = button.dataset.mood;

      // Update UI state
      moodButtons.forEach((btn) => btn.classList.remove("active"));
      button.classList.add("active");

      if (currentMood === mood) return;
      currentMood = mood;

      // Show loading state
      playlistsContainer.innerHTML = "";
      loadingSection.classList.remove("hidden");
      errorSection.classList.add("hidden");
      emptySection.classList.add("hidden");

      try {
        const response = await fetch(
          `/spotify/public/api/playlists.php?mood=${mood}`
        );
        const data = await response.json();

        if (!response.ok)
          throw new Error(data.message || "Failed to fetch playlists");

        // Hide loading state
        loadingSection.classList.add("hidden");

        if (data.data.length === 0) {
          emptySection.classList.remove("hidden");
          return;
        }

        // Render playlists
        renderPlaylists(data.data);
      } catch (error) {
        loadingSection.classList.add("hidden");
        errorSection.classList.remove("hidden");
        errorMessage.textContent = error.message;
      }
    });
  });
});

function formatFollowers(count) {
  if (count >= 1000000) {
    return `${(count / 1000000).toFixed(1)}M`;
  }
  if (count >= 1000) {
    return `${(count / 1000).toFixed(1)}K`;
  }
  return count.toString();
}

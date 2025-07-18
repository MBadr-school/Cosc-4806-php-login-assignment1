<?php require_once 'app/views/templates/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="hero-section bg-primary text-white py-5 mb-4 rounded">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h1 class="display-4 fw-bold mb-3">🎬 Movie Search & Reviews</h1>
                            <p class="lead mb-4">Discover movies, read AI-generated reviews, and share your ratings with the community</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="d-flex justify-content-end">
                                <i class="bi bi-film display-1 opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Section -->
            <div class="search-section mb-5">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="card shadow-sm border-0">
                                <div class="card-body p-4">
                                    <form id="movie-search-form" class="d-flex gap-3">
                                        <div class="input-group flex-grow-1">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-search"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control border-start-0 ps-0" 
                                                   id="search-query" 
                                                   placeholder="Search for movies (e.g., 'The Matrix', 'Inception')..." 
                                                   required>
                                        </div>
                                        <button type="submit" class="btn btn-primary px-4">
                                            <span class="search-text">Search</span>
                                            <span class="spinner-border spinner-border-sm search-spinner d-none" role="status"></span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Results Section -->
            <div id="search-results" class="container d-none">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="h3 mb-0">Search Results</h2>
                            <div class="pagination-info text-muted"></div>
                        </div>
                        <div id="movies-grid" class="row g-4">
                            <!-- Movies will be populated here -->
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            <nav>
                                <ul class="pagination" id="search-pagination">
                                    <!-- Pagination will be populated here -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popular Movies Section -->
            <div class="popular-section mt-5">
                <div class="container">
                    <h2 class="h3 mb-4">🔥 Popular Movies</h2>
                    <div id="popular-movies" class="row g-4">
                        <!-- Popular movies will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Recent Movies Section -->
            <div class="recent-section mt-5">
                <div class="container">
                    <h2 class="h3 mb-4">🕐 Recently Rated</h2>
                    <div id="recent-movies" class="row g-4">
                        <!-- Recent movies will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Movie Details Modal -->
<div class="modal fade" id="movieModal" tabindex="-1" aria-labelledby="movieModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title" id="movieModalLabel">Movie Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="movie-details-content">
                <!-- Movie details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title" id="reviewModalLabel">AI Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="review-content">
                <!-- Review content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="position-fixed top-0 start-0 w-100 h-100 d-none" style="z-index: 9999; background: rgba(0,0,0,0.5);">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="text-center text-white">
            <div class="spinner-border mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div>Loading...</div>
        </div>
    </div>
</div>

<!-- Custom CSS -->
<style>
    .hero-section {
        background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    }

    .movie-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border: none;
        height: 100%;
    }

    .movie-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .movie-poster {
        height: 400px;
        object-fit: cover;
        border-radius: 8px;
    }

    .rating-stars {
        color: #ffc107;
        font-size: 1.2rem;
    }

    .rating-input {
        cursor: pointer;
        transition: color 0.2s ease;
    }

    .rating-input:hover {
        color: #ffc107;
    }

    .rating-input.active {
        color: #ffc107;
    }

    .btn-outline-primary:hover {
        background-color: #4a90e2;
        border-color: #4a90e2;
    }

    .badge {
        font-size: 0.75rem;
    }

    .search-section .card {
        border: 1px solid #e3f2fd;
    }

    .input-group-text {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }

    .pagination .page-link {
        color: #4a90e2;
        border-color: #dee2e6;
    }

    .pagination .page-link:hover {
        background-color: #e3f2fd;
        border-color: #4a90e2;
    }

    .pagination .page-item.active .page-link {
        background-color: #4a90e2;
        border-color: #4a90e2;
    }

    .text-muted {
        color: #6c757d !important;
    }

    .movie-meta {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .review-section {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-top: 1rem;
    }

    .rating-distribution {
        font-size: 0.85rem;
    }

    .progress {
        height: 0.5rem;
    }

    .modal-content {
        border: none;
        border-radius: 12px;
    }

    .modal-header {
        background-color: #f8f9fa;
        border-radius: 12px 12px 0 0;
    }

    @media (max-width: 768px) {
        .hero-section {
            text-align: center;
        }

        .movie-poster {
            height: 300px;
        }

        .display-4 {
            font-size: 2rem;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Movie search and display functionality
class MovieApp {
    constructor() {
        this.currentPage = 1;
        this.currentQuery = '';
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadPopularMovies();
        this.loadRecentMovies();
    }

    bindEvents() {
        // Search form submission
        document.getElementById('movie-search-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.searchMovies();
        });

        // Real-time search (optional)
        document.getElementById('search-query').addEventListener('input', (e) => {
            const query = e.target.value.trim();
            if (query.length > 2) {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.searchMovies();
                }, 500);
            }
        });
    }

    searchMovies(page = 1) {
        const query = document.getElementById('search-query').value.trim();

        if (!query) {
            this.showError('Please enter a movie title to search');
            return;
        }

        this.currentQuery = query;
        this.currentPage = page;

        this.showLoading('search');

        fetch('/movie/search', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `query=${encodeURIComponent(query)}&page=${page}`
        })
        .then(response => response.json())
        .then(data => {
            this.hideLoading('search');

            if (data.error) {
                this.showError(data.error);
                return;
            }

            if (data.Search) {
                this.displaySearchResults(data);
            } else {
                this.showError('No movies found for your search');
            }
        })
        .catch(error => {
            this.hideLoading('search');
            this.showError('Search failed. Please try again.');
            console.error('Search error:', error);
        });
    }

    displaySearchResults(data) {
        const resultsContainer = document.getElementById('search-results');
        const moviesGrid = document.getElementById('movies-grid');

        resultsContainer.classList.remove('d-none');
        moviesGrid.innerHTML = '';

        // Update pagination info
        const paginationInfo = document.querySelector('.pagination-info');
        const totalResults = parseInt(data.totalResults);
        const currentPage = this.currentPage;
        const totalPages = Math.ceil(totalResults / 10);

        paginationInfo.textContent = `Page ${currentPage} of ${totalPages} (${totalResults} movies found)`;

        // Display movies
        data.Search.forEach(movie => {
            const movieCard = this.createMovieCard(movie);
            moviesGrid.appendChild(movieCard);
        });

        // Update pagination
        this.updatePagination(totalPages);

        // Scroll to results
        resultsContainer.scrollIntoView({ behavior: 'smooth' });
    }

    createMovieCard(movie) {
        const col = document.createElement('div');
        col.className = 'col-lg-3 col-md-4 col-sm-6';

        const ratingStars = this.generateStarRating(movie.rating_stats?.average_rating || 0);
        const userRating = movie.user_rating || 0;

        col.innerHTML = `
            <div class="card movie-card shadow-sm h-100">
                <img src="${movie.Poster !== 'N/A' ? movie.Poster : '/assets/placeholder-movie.jpg'}" 
                     class="card-img-top movie-poster" 
                     alt="${movie.Title}"
                     onerror="this.src='/assets/placeholder-movie.jpg'">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">${movie.Title}</h5>
                    <div class="movie-meta mb-2">
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> ${movie.Year}
                        </small>
                    </div>

                    <div class="rating-section mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rating-stars me-2">${ratingStars}</div>
                            <small class="text-muted">
                                (${movie.rating_stats?.total_ratings || 0} ratings)
                            </small>
                        </div>

                        <div class="user-rating">
                            <small class="text-muted me-2">Your rating:</small>
                            <div class="rating-input d-inline-flex" data-imdb-id="${movie.imdbID}">
                                ${this.generateInteractiveStars(userRating)}
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto">
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm flex-grow-1" 
                                    onclick="movieApp.showMovieDetails('${movie.imdbID}')">
                                <i class="bi bi-info-circle"></i> Details
                            </button>
                            <button class="btn btn-outline-success btn-sm" 
                                    onclick="movieApp.getReview('${movie.imdbID}')">
                                <i class="bi bi-robot"></i> Review
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Add rating event listeners
        const ratingInputs = col.querySelectorAll('.rating-input i');
        ratingInputs.forEach((star, index) => {
            star.addEventListener('click', () => {
                this.rateMovie(movie.imdbID, index + 1);
            });
        });

        return col;
    }

    generateStarRating(rating) {
        const fullStars = Math.floor(rating);
        const halfStar = rating % 1 >= 0.5;
        const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);

        let stars = '';
        for (let i = 0; i < fullStars; i++) {
            stars += '<i class="bi bi-star-fill"></i>';
        }
        if (halfStar) {
            stars += '<i class="bi bi-star-half"></i>';
        }
        for (let i = 0; i < emptyStars; i++) {
            stars += '<i class="bi bi-star"></i>';
        }

        return stars;
    }

    generateInteractiveStars(currentRating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            const filled = i <= currentRating;
            stars += `<i class="bi bi-star${filled ? '-fill' : ''} rating-star ${filled ? 'active' : ''}" 
                        data-rating="${i}" 
                        style="cursor: pointer; color: ${filled ? '#ffc107' : '#dee2e6'}"></i>`;
        }
        return stars;
    }

    rateMovie(imdbId, rating) {
        fetch('/movie/rate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `imdb_id=${imdbId}&rating=${rating}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showSuccess('Rating saved successfully!');
                this.updateMovieRating(imdbId, data);
            } else {
                this.showError(data.error || 'Rating failed');
            }
        })
        .catch(error => {
            this.showError('Rating failed. Please try again.');
            console.error('Rating error:', error);
        });
    }

    updateMovieRating(imdbId, data) {
        // Update the rating display for the movie
        const ratingInput = document.querySelector(`[data-imdb-id="${imdbId}"]`);
        if (ratingInput) {
            ratingInput.innerHTML = this.generateInteractiveStars(data.user_rating);

            // Re-bind event listeners
            const stars = ratingInput.querySelectorAll('.rating-star');
            stars.forEach((star, index) => {
                star.addEventListener('click', () => {
                    this.rateMovie(imdbId, index + 1);
                });
            });
        }
    }

    showMovieDetails(imdbId) {
        this.showLoading('modal');

        fetch('/movie/details', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `imdb_id=${imdbId}`
        })
        .then(response => response.json())
        .then(data => {
            this.hideLoading('modal');

            if (data.error) {
                this.showError(data.error);
                return;
            }

            this.displayMovieDetails(data);
        })
        .catch(error => {
            this.hideLoading('modal');
            this.showError('Failed to load movie details');
            console.error('Details error:', error);
        });
    }

    displayMovieDetails(movie) {
        const modalContent = document.getElementById('movie-details-content');
        const modalLabel = document.getElementById('movieModalLabel');

        modalLabel.textContent = movie.Title;

        const ratingStars = this.generateStarRating(movie.rating_stats?.average_rating || 0);
        const userRating = movie.user_rating || 0;

        modalContent.innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <img src="${movie.Poster !== 'N/A' ? movie.Poster : '/assets/placeholder-movie.jpg'}" 
                         class="img-fluid rounded shadow-sm" 
                         alt="${movie.Title}">
                </div>
                <div class="col-md-8">
                    <h4>${movie.Title} (${movie.Year})</h4>

                    <div class="movie-meta mb-3">
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <small class="text-muted">
                                    <i class="bi bi-clock"></i> ${movie.Runtime || 'N/A'}
                                </small>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted">
                                    <i class="bi bi-tag"></i> ${movie.Genre || 'N/A'}
                                </small>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted">
                                    <i class="bi bi-person"></i> ${movie.Director || 'N/A'}
                                </small>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted">
                                    <i class="bi bi-star"></i> IMDB: ${movie.imdbRating || 'N/A'}
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="rating-section mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rating-stars me-2">${ratingStars}</div>
                            <span class="badge bg-primary">
                                ${movie.rating_stats?.average_rating || 0}/5
                            </span>
                            <small class="text-muted ms-2">
                                (${movie.rating_stats?.total_ratings || 0} ratings)
                            </small>
                        </div>

                        <div class="user-rating">
                            <small class="text-muted me-2">Your rating:</small>
                            <div class="rating-input d-inline-flex" data-imdb-id="${movie.imdbID}">
                                ${this.generateInteractiveStars(userRating)}
                            </div>
                        </div>
                    </div>

                    <div class="plot-section mb-3">
                        <h6>Plot</h6>
                        <p class="text-muted">${movie.Plot || 'No plot available'}</p>
                    </div>

                    <div class="cast-section mb-3">
                        <h6>Cast</h6>
                        <p class="text-muted">${movie.Actors || 'N/A'}</p>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" onclick="movieApp.getReview('${movie.imdbID}')">
                            <i class="bi bi-robot"></i> Get AI Review
                        </button>
                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Add rating event listeners
        const ratingInputs = modalContent.querySelectorAll('.rating-input i');
        ratingInputs.forEach((star, index) => {
            star.addEventListener('click', () => {
                this.rateMovie(movie.imdbID, index + 1);
            });
        });

        const modal = new bootstrap.Modal(document.getElementById('movieModal'));
        modal.show();
    }

    getReview(imdbId) {
        this.showLoading('review');

        fetch('/movie/review', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `imdb_id=${imdbId}`
        })
        .then(response => response.json())
        .then(data => {
            this.hideLoading('review');

            if (data.error) {
                this.showError(data.error);
                return;
            }

            this.displayReview(data);
        })
        .catch(error => {
            this.hideLoading('review');
            this.showError('Failed to generate review');
            console.error('Review error:', error);
        });
    }

    displayReview(data) {
        const reviewContent = document.getElementById('review-content');
        const reviewLabel = document.getElementById('reviewModalLabel');

        reviewLabel.textContent = `AI Review: ${data.movie_title}`;

        reviewContent.innerHTML = `
            <div class="review-section">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-robot text-primary fs-4 me-2"></i>
                    <h6 class="mb-0">AI-Generated Review</h6>
                </div>
                <div class="review-text">
                    ${data.review.replace(/\n/g, '</p><p>')}
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> 
                        This review was generated using AI and may not reflect all aspects of the movie.
                    </small>
                </div>
            </div>
        `;

        const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
        modal.show();
    }

    loadPopularMovies() {
        fetch('/movie/popular?limit=4')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.movies) {
                this.displayMovieList(data.movies, 'popular-movies');
            }
        })
        .catch(error => {
            console.error('Failed to load popular movies:', error);
        });
    }

    loadRecentMovies() {
        fetch('/movie/recent?limit=4')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.movies) {
                this.displayMovieList(data.movies, 'recent-movies');
            }
        })
        .catch(error => {
            console.error('Failed to load recent movies:', error);
        });
    }

    displayMovieList(movies, containerId) {
        const container = document.getElementById(containerId);
        container.innerHTML = '';

        movies.forEach(movie => {
            const movieCard = this.createMovieCard(movie);
            container.appendChild(movieCard);
        });
    }

    updatePagination(totalPages) {
        const pagination = document.getElementById('search-pagination');
        pagination.innerHTML = '';

        if (totalPages <= 1) return;

        const currentPage = this.currentPage;

        // Previous button
        if (currentPage > 1) {
            pagination.innerHTML += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="movieApp.searchMovies(${currentPage - 1})">Previous</a>
                </li>
            `;
        }

        // Page numbers
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);

        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === currentPage ? 'active' : '';
            pagination.innerHTML += `
                <li class="page-item ${isActive}">
                    <a class="page-link" href="#" onclick="movieApp.searchMovies(${i})">${i}</a>
                </li>
            `;
        }

        // Next button
        if (currentPage < totalPages) {
            pagination.innerHTML += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="movieApp.searchMovies(${currentPage + 1})">Next</a>
                </li>
            `;
        }
    }

    showLoading(type) {
        if (type === 'search') {
            const button = document.querySelector('#movie-search-form button');
            const text = button.querySelector('.search-text');
            const spinner = button.querySelector('.search-spinner');

            text.classList.add('d-none');
            spinner.classList.remove('d-none');
            button.disabled = true;
        } else if (type === 'modal' || type === 'review') {
            document.getElementById('loading-overlay').classList.remove('d-none');
        }
    }

    hideLoading(type) {
        if (type === 'search') {
            const button = document.querySelector('#movie-search-form button');
            const text = button.querySelector('.search-text');
            const spinner = button.querySelector('.search-spinner');

            text.classList.remove('d-none');
            spinner.classList.add('d-none');
            button.disabled = false;
        } else if (type === 'modal' || type === 'review') {
            document.getElementById('loading-overlay').classList.add('d-none');
        }
    }

    showError(message) {
        this.showToast(message, 'error');
    }

    showSuccess(message) {
        this.showToast(message, 'success');
    }

    showToast(message, type) {
        const toastContainer = document.querySelector('.toast-container') || this.createToastContainer();

        const toastId = 'toast-' + Date.now();
        const bgClass = type === 'error' ? 'bg-danger' : 'bg-success';

        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white ${bgClass} border-0`;
        toast.id = toastId;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        toastContainer.appendChild(toast);

        const bootstrapToast = new bootstrap.Toast(toast);
        bootstrapToast.show();

        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1100';
        document.body.appendChild(container);
        return container;
    }
}

// Initialize the movie app
const movieApp = new MovieApp();
</script>

<?php require_once 'app/views/templates/footer.php'; ?> 
/**
 * Project Category Filter - Frontend JavaScript
 * Provides real-time AJAX filtering with progressive enhancement
 */

(function () {
    'use strict';

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        const filterContainer = document.querySelector('.project-category-filter');
        if (!filterContainer) return;

        const queryLoop = document.querySelector('.wp-block-query');
        if (!queryLoop) {
            console.warn('No Query Loop block found - filter will use standard page navigation');
            return;
        }

        const taxonomy = filterContainer.getAttribute('data-taxonomy') || 'category';
        const displayStyle = filterContainer.getAttribute('data-display-style') || 'buttons';

        // Initialize based on display style
        if (displayStyle === 'dropdown') {
            initDropdownFilter(filterContainer, queryLoop, taxonomy);
        } else {
            initLinkFilter(filterContainer, queryLoop, taxonomy);
        }
    }

    /**
     * Initialize dropdown-style filter
     */
    function initDropdownFilter(filterContainer, queryLoop, taxonomy) {
        const select = filterContainer.querySelector('.project-category-filter__select');
        if (!select) return;

        select.addEventListener('change', function (e) {
            const termId = e.target.value;
            const selectedOption = e.target.options[e.target.selectedIndex];
            const url = selectedOption.getAttribute('data-url');

            // If no term selected (All Projects)
            if (!termId) {
                filterProjects(queryLoop, null, taxonomy, filterContainer, url);
            } else {
                filterProjects(queryLoop, termId, taxonomy, filterContainer, url);
            }
        });
    }

    /**
     * Initialize link/button-style filter
     */
    function initLinkFilter(filterContainer, queryLoop, taxonomy) {
        const links = filterContainer.querySelectorAll('[data-filter-link]');
        if (!links.length) return;

        links.forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();

                const termId = link.getAttribute('data-term-id');
                const url = link.getAttribute('href');

                // Update active state
                links.forEach(function (l) {
                    l.classList.remove('is-active');
                });
                link.classList.add('is-active');

                // Filter projects
                if (termId === '0') {
                    filterProjects(queryLoop, null, taxonomy, filterContainer, url);
                } else {
                    filterProjects(queryLoop, termId, taxonomy, filterContainer, url);
                }
            });
        });
    }

    /**
     * Filter projects using WordPress REST API
     */
    function filterProjects(queryLoop, termId, taxonomy, filterContainer, fallbackUrl) {
        const loadingIndicator = filterContainer.querySelector('.project-category-filter__loading');
        const postList = queryLoop.querySelector('.wp-block-post-template');
        const pagination = queryLoop.querySelector('.wp-block-query-pagination');

        if (!postList) {
            console.error('Could not find post template in Query Loop');
            window.location.href = fallbackUrl;
            return;
        }

        // Get the template post element before clearing
        const templatePost = postList.querySelector('.wp-block-post');
        if (!templatePost) {
            console.error('No post template found - using page navigation');
            window.location.href = fallbackUrl;
            return;
        }

        // Clone the template for reuse
        const postTemplate = templatePost.cloneNode(true);

        // Show loading state
        if (loadingIndicator) {
            loadingIndicator.style.display = 'flex';
        }
        postList.style.opacity = '0.5';

        // Build REST API URL
        let apiUrl = '/wp-json/wp/v2/posts?_embed=true&per_page=12';

        // Use correct REST API parameter names
        if (termId) {
            if (taxonomy === 'category') {
                apiUrl += '&categories=' + termId; // REST API uses plural
            } else if (taxonomy === 'post_tag') {
                apiUrl += '&tags=' + termId; // REST API uses plural
            }
        }

        // Fetch filtered posts
        fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin'
        })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(function (posts) {
            // Update the post list using the template
            renderPosts(posts, postList, postTemplate);

            // Hide pagination during filtered view
            if (pagination) {
                pagination.style.display = 'none';
            }

            // Update browser history (optional - for back button support)
            if (window.history && window.history.pushState) {
                const newUrl = termId ? fallbackUrl : window.location.pathname;
                window.history.pushState({ termId: termId }, '', newUrl);
            }

            // Scroll to results
            queryLoop.scrollIntoView({ behavior: 'smooth', block: 'start' });
        })
        .catch(function (error) {
            console.error('Error filtering projects:', error);
            // Fallback to page navigation if AJAX fails
            window.location.href = fallbackUrl;
        })
        .finally(function () {
            // Hide loading state
            if (loadingIndicator) {
                loadingIndicator.style.display = 'none';
            }
            postList.style.opacity = '1';
        });
    }

    /**
     * Render posts by cloning the existing template
     */
    function renderPosts(posts, postList, postTemplate) {
        // Clear current posts
        postList.innerHTML = '';

        if (!posts || posts.length === 0) {
            const noResults = postTemplate.cloneNode(true);
            noResults.innerHTML = '<div style="width: 100%; text-align: center; padding: 40px; grid-column: 1 / -1;">' +
                '<p style="font-size: 1.2em; color: #666;">No projects found for this category.</p>' +
                '</div>';
            postList.appendChild(noResults);
            return;
        }

        // Create posts by cloning and updating the template
        posts.forEach(function (post) {
            const postItem = postTemplate.cloneNode(true);
            updatePostContent(postItem, post);
            postList.appendChild(postItem);
        });
    }

    /**
     * Update post content in the cloned template
     */
    function updatePostContent(postElement, post) {
        // Update featured image
        const featuredImage = postElement.querySelector('.wp-block-post-featured-image img');
        if (featuredImage && post._embedded && post._embedded['wp:featuredmedia']) {
            const media = post._embedded['wp:featuredmedia'][0];
            const imageUrl = media.media_details && media.media_details.sizes && media.media_details.sizes.medium
                ? media.media_details.sizes.medium.source_url
                : media.source_url;
            featuredImage.src = imageUrl;
            featuredImage.alt = post.title.rendered;
        }

        // Update featured image link
        const imageLink = postElement.querySelector('.wp-block-post-featured-image a');
        if (imageLink) {
            imageLink.href = post.link;
        }

        // Update title
        const titleElement = postElement.querySelector('.wp-block-post-title');
        if (titleElement) {
            titleElement.innerHTML = '<a href="' + escapeHtml(post.link) + '">' + post.title.rendered + '</a>';
        }

        // Update title link (if title itself is not a link)
        const titleLink = postElement.querySelector('.wp-block-post-title a');
        if (titleLink) {
            titleLink.href = post.link;
            titleLink.innerHTML = post.title.rendered;
        }

        // Update excerpt
        const excerptElement = postElement.querySelector('.wp-block-post-excerpt__excerpt');
        if (excerptElement && post.excerpt) {
            excerptElement.innerHTML = post.excerpt.rendered;
        }

        // Update date
        const dateElement = postElement.querySelector('.wp-block-post-date time');
        if (dateElement) {
            const date = new Date(post.date);
            dateElement.setAttribute('datetime', post.date);
            dateElement.textContent = date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        // Update categories/terms
        const termsElement = postElement.querySelector('.wp-block-post-terms');
        if (termsElement && post._embedded && post._embedded['wp:term']) {
            const categories = post._embedded['wp:term'][0] || [];
            if (categories.length > 0) {
                termsElement.innerHTML = categories.map(function(term) {
                    return '<a href="' + escapeHtml(term.link) + '" rel="tag">' + escapeHtml(term.name) + '</a>';
                }).join(' ');
            }
        }
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function (m) { return map[m]; });
    }

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function (event) {
        if (event.state && event.state.termId !== undefined) {
            // Reload the page to show the correct filtered state
            window.location.reload();
        }
    });

})();

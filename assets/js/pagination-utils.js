// Pagination utility functions for all tables
class PaginationUtils {
    static renderPagination(containerId, currentPage, totalItems, itemsPerPage, onPageChange) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const pagination = document.getElementById(containerId);
        
        if (totalPages <= 1) {
            pagination.innerHTML = '';
            return;
        }

        // Check if current page has minimum items (full page)
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const currentPageItems = Math.min(itemsPerPage, totalItems - startIndex);
        const isCurrentPageFull = currentPageItems >= itemsPerPage;

        let paginationHTML = '';
        
        // Previous button
        paginationHTML += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="${onPageChange}(${currentPage - 1})">Previous</a>
        </li>`;

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                paginationHTML += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="${onPageChange}(${i})">${i}</a>
                </li>`;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                paginationHTML += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Next button - only show if current page is full (has minimum items per page)
        const shouldShowNext = currentPage < totalPages && isCurrentPageFull;
        paginationHTML += `<li class="page-item ${!shouldShowNext ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="${onPageChange}(${currentPage + 1})" ${!shouldShowNext ? 'style="pointer-events: none;"' : ''}>Next</a>
        </li>`;

        pagination.innerHTML = paginationHTML;
    }

    static validatePageChange(newPage, currentPage, totalItems, itemsPerPage) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        
        // For "Next" button, check if current page is full before allowing navigation
        if (newPage > currentPage) {
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const currentPageItems = Math.min(itemsPerPage, totalItems - startIndex);
            const isCurrentPageFull = currentPageItems >= itemsPerPage;
            
            if (!isCurrentPageFull) {
                return false; // Don't allow navigation to next page if current page is not full
            }
        }
        
        return newPage >= 1 && newPage <= totalPages;
    }

    static getPageData(items, currentPage, itemsPerPage) {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        return items.slice(startIndex, endIndex);
    }
}

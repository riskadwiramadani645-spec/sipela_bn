/**
 * Enhanced DataTable System
 * Provides search, pagination, and sorting functionality
 */

class EnhancedDataTable {
    constructor(tableId, options = {}) {
        this.table = document.getElementById(tableId);
        this.options = {
            pageSize: options.pageSize || 10,
            searchable: options.searchable !== false,
            sortable: options.sortable !== false,
            pageSizes: options.pageSizes || [10, 25, 50, 100],
            defaultSort: options.defaultSort || null,
            ...options
        };
        
        this.currentPage = 1;
        this.currentSort = { column: null, direction: null };
        this.searchTerm = '';
        this.allRows = [];
        this.filteredRows = [];
        
        this.init();
    }
    
    init() {
        if (!this.table) return;
        
        this.wrapTable();
        this.setupHeader();
        this.setupFooter();
        this.collectRows();
        this.setupEventListeners();
        this.applyDefaultSort();
        this.render();
    }
    
    wrapTable() {
        const wrapper = document.createElement('div');
        wrapper.className = 'datatable-wrapper';
        
        this.table.parentNode.insertBefore(wrapper, this.table);
        wrapper.appendChild(this.table);
        
        this.wrapper = wrapper;
        this.table.className += ' enhanced-table';
    }
    
    setupHeader() {
        const header = document.createElement('div');
        header.className = 'datatable-header';
        
        // Length selector
        const lengthDiv = document.createElement('div');
        lengthDiv.className = 'datatable-length';
        lengthDiv.innerHTML = `
            <span>Tampilkan</span>
            <select id="pageSize_${this.table.id}">
                ${this.options.pageSizes.map(size => 
                    `<option value="${size}" ${size === this.options.pageSize ? 'selected' : ''}>${size}</option>`
                ).join('')}
            </select>
            <span>data</span>
        `;
        
        // Search and export
        const searchDiv = document.createElement('div');
        searchDiv.className = 'datatable-search';
        searchDiv.innerHTML = `
            <div class="search-box">
                <input type="text" id="search_${this.table.id}" placeholder="Cari nama, NISN, atau data lainnya...">
                <i class="fas fa-search search-icon"></i>
            </div>
        `;
        
        header.appendChild(lengthDiv);
        header.appendChild(searchDiv);
        
        this.wrapper.insertBefore(header, this.table);
        this.header = header;
    }
    
    setupFooter() {
        const footer = document.createElement('div');
        footer.className = 'datatable-info';
        footer.innerHTML = `
            <div class="info-text"></div>
            <div class="pagination-wrapper"></div>
        `;
        
        this.wrapper.appendChild(footer);
        this.footer = footer;
    }
    
    collectRows() {
        const tbody = this.table.querySelector('tbody');
        if (!tbody) return;
        
        this.allRows = Array.from(tbody.querySelectorAll('tr')).map((row, index) => ({
            element: row,
            index: index,
            data: Array.from(row.cells).map(cell => cell.textContent.trim().toLowerCase())
        }));
        
        this.filteredRows = [...this.allRows];
    }
    
    setupEventListeners() {
        // Page size change
        const pageSizeSelect = document.getElementById(`pageSize_${this.table.id}`);
        if (pageSizeSelect) {
            pageSizeSelect.addEventListener('change', (e) => {
                this.options.pageSize = parseInt(e.target.value);
                this.currentPage = 1;
                this.render();
            });
        }
        
        // Search
        const searchInput = document.getElementById(`search_${this.table.id}`);
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.searchTerm = e.target.value.toLowerCase();
                this.filterRows();
                this.currentPage = 1;
                this.render();
            });
        }
        
        // Sortable headers
        if (this.options.sortable) {
            const headers = this.table.querySelectorAll('th');
            headers.forEach((header, index) => {
                if (header.textContent.trim() && !header.classList.contains('no-sort')) {
                    header.classList.add('sortable');
                    header.addEventListener('click', () => this.sortColumn(index));
                }
            });
        }
    }
    
    applyDefaultSort() {
        if (this.options.defaultSort) {
            const { column, direction } = this.options.defaultSort;
            this.currentSort = { column, direction };
            this.sortColumn(column, direction);
        } else {
            // Auto-detect date column for default sorting
            const headers = this.table.querySelectorAll('th');
            let dateColumnIndex = -1;
            
            headers.forEach((header, index) => {
                const text = header.textContent.toLowerCase();
                if (text.includes('tanggal') || text.includes('date') || text.includes('dibuat')) {
                    dateColumnIndex = index;
                }
            });
            
            if (dateColumnIndex !== -1) {
                this.currentSort = { column: dateColumnIndex, direction: 'desc' };
                this.sortColumn(dateColumnIndex, 'desc');
            }
        }
    }
    
    filterRows() {
        if (!this.searchTerm) {
            this.filteredRows = [...this.allRows];
            return;
        }
        
        this.filteredRows = this.allRows.filter(row => 
            row.data.some(cellData => cellData.includes(this.searchTerm))
        );
    }
    
    sortColumn(columnIndex, forceDirection = null) {
        let newDirection;
        
        if (forceDirection) {
            newDirection = forceDirection;
        } else {
            const currentDirection = this.currentSort.column === columnIndex ? this.currentSort.direction : null;
            newDirection = 'asc';
            
            if (currentDirection === 'asc') {
                newDirection = 'desc';
            } else if (currentDirection === 'desc') {
                newDirection = null;
            }
        }
        
        // Update sort state
        this.currentSort = {
            column: newDirection ? columnIndex : null,
            direction: newDirection
        };
        
        // Update header classes
        const headers = this.table.querySelectorAll('th');
        headers.forEach((header, index) => {
            header.classList.remove('sort-asc', 'sort-desc');
            if (index === columnIndex && newDirection) {
                header.classList.add(`sort-${newDirection}`);
            }
        });
        
        // Sort rows
        if (newDirection) {
            this.filteredRows.sort((a, b) => {
                const aVal = a.data[columnIndex] || '';
                const bVal = b.data[columnIndex] || '';
                
                const result = aVal.localeCompare(bVal, 'id', { numeric: true });
                return newDirection === 'asc' ? result : -result;
            });
        } else {
            // Reset to original order
            this.filterRows();
        }
        
        this.currentPage = 1;
        this.render();
    }
    
    render() {
        this.renderTable();
        this.renderPagination();
        this.renderInfo();
    }
    
    renderTable() {
        const tbody = this.table.querySelector('tbody');
        if (!tbody) return;
        
        // Hide all rows
        this.allRows.forEach(row => {
            row.element.style.display = 'none';
            row.element.classList.remove('hidden');
        });
        
        // Show current page rows
        const startIndex = (this.currentPage - 1) * this.options.pageSize;
        const endIndex = startIndex + this.options.pageSize;
        const pageRows = this.filteredRows.slice(startIndex, endIndex);
        
        pageRows.forEach(row => {
            row.element.style.display = '';
            this.highlightSearchTerm(row.element);
        });
        
        // Show no data message if needed
        if (this.filteredRows.length === 0) {
            this.showNoData();
        } else {
            this.hideNoData();
        }
    }
    
    highlightSearchTerm(row) {
        if (!this.searchTerm) return;
        
        const cells = row.querySelectorAll('td');
        cells.forEach(cell => {
            const originalText = cell.textContent;
            if (originalText.toLowerCase().includes(this.searchTerm)) {
                const regex = new RegExp(`(${this.searchTerm})`, 'gi');
                cell.innerHTML = originalText.replace(regex, '<span class="highlight">$1</span>');
            }
        });
    }
    
    renderPagination() {
        const totalPages = Math.ceil(this.filteredRows.length / this.options.pageSize);
        const paginationWrapper = this.footer.querySelector('.pagination-wrapper');
        
        if (totalPages <= 1) {
            paginationWrapper.innerHTML = '';
            return;
        }
        
        let paginationHTML = '';
        
        // Previous button
        paginationHTML += `
            <button class="pagination-btn" ${this.currentPage === 1 ? 'disabled' : ''} 
                    onclick="window.datatables['${this.table.id}'].goToPage(${this.currentPage - 1})">
                <i class="fas fa-chevron-left"></i>
            </button>
        `;
        
        // Page numbers
        const startPage = Math.max(1, this.currentPage - 2);
        const endPage = Math.min(totalPages, this.currentPage + 2);
        
        if (startPage > 1) {
            paginationHTML += `<button class="pagination-btn" onclick="window.datatables['${this.table.id}'].goToPage(1)">1</button>`;
            if (startPage > 2) {
                paginationHTML += `<span class="pagination-ellipsis">...</span>`;
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <button class="pagination-btn ${i === this.currentPage ? 'active' : ''}" 
                        onclick="window.datatables['${this.table.id}'].goToPage(${i})">${i}</button>
            `;
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                paginationHTML += `<span class="pagination-ellipsis">...</span>`;
            }
            paginationHTML += `<button class="pagination-btn" onclick="window.datatables['${this.table.id}'].goToPage(${totalPages})">${totalPages}</button>`;
        }
        
        // Next button
        paginationHTML += `
            <button class="pagination-btn" ${this.currentPage === totalPages ? 'disabled' : ''} 
                    onclick="window.datatables['${this.table.id}'].goToPage(${this.currentPage + 1})">
                <i class="fas fa-chevron-right"></i>
            </button>
        `;
        
        paginationWrapper.innerHTML = paginationHTML;
    }
    
    renderInfo() {
        const infoText = this.footer.querySelector('.info-text');
        const startIndex = (this.currentPage - 1) * this.options.pageSize + 1;
        const endIndex = Math.min(this.currentPage * this.options.pageSize, this.filteredRows.length);
        
        if (this.filteredRows.length === 0) {
            infoText.textContent = 'Tidak ada data yang ditemukan';
        } else {
            infoText.textContent = `Menampilkan ${startIndex} sampai ${endIndex} dari ${this.filteredRows.length} data`;
            if (this.filteredRows.length !== this.allRows.length) {
                infoText.textContent += ` (difilter dari ${this.allRows.length} total data)`;
            }
        }
    }
    
    goToPage(page) {
        const totalPages = Math.ceil(this.filteredRows.length / this.options.pageSize);
        if (page >= 1 && page <= totalPages) {
            this.currentPage = page;
            this.render();
        }
    }
    
    showNoData() {
        let noDataRow = this.table.querySelector('.no-data-row');
        if (!noDataRow) {
            const tbody = this.table.querySelector('tbody');
            const colCount = this.table.querySelectorAll('th').length;
            
            noDataRow = document.createElement('tr');
            noDataRow.className = 'no-data-row';
            noDataRow.innerHTML = `<td colspan="${colCount}" class="no-data">Tidak ada data yang ditemukan</td>`;
            
            tbody.appendChild(noDataRow);
        }
        noDataRow.style.display = '';
    }
    
    hideNoData() {
        const noDataRow = this.table.querySelector('.no-data-row');
        if (noDataRow) {
            noDataRow.style.display = 'none';
        }
    }
}

// Global datatables registry
window.datatables = window.datatables || {};

// Auto-initialize tables with data-datatable attribute
document.addEventListener('DOMContentLoaded', function() {
    const tables = document.querySelectorAll('[data-datatable]');
    tables.forEach(table => {
        // Ensure table has proper styling classes
        if (!table.classList.contains('enhanced-table')) {
            table.classList.add('enhanced-table');
        }
        
        // Ensure table is wrapped in table-responsive if not already
        if (!table.closest('.table-responsive')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
        
        const options = {};
        
        // Parse options from data attributes
        if (table.dataset.pageSize) options.pageSize = parseInt(table.dataset.pageSize);
        if (table.dataset.searchable === 'false') options.searchable = false;
        if (table.dataset.sortable === 'false') options.sortable = false;
        
        window.datatables[table.id] = new EnhancedDataTable(table.id, options);
    });
});

// Export function for manual initialization
window.initDataTable = function(tableId, options = {}) {
    window.datatables[tableId] = new EnhancedDataTable(tableId, options);
    return window.datatables[tableId];
};
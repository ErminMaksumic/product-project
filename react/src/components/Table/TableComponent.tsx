import React from "react";
import Link from "next/link";
import { Product } from "@/lib/product";
import styles from "./TableComponent.module.scss"; // Import the SCSS module

interface TableProps {
    products?: Product[];
    currentPage: number;
    totalPages: number;
    handlePageChange: (page: number) => void;
}

const TableComponent: React.FC<TableProps> = ({
    products,
    currentPage,
    totalPages,
    handlePageChange,
}) => {
    if (products === undefined) {
        return <div className={styles.loading}>Loading...</div>;
    }

    return (
        <div className={styles.tableContainer}>
            <table className={styles.table}>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    {products.map((product) => (
                        <tr key={product.id}>
                            <td>{product.id}</td>
                            <td>{product.name}</td>
                            <td>{product.status}</td>
                            <td>
                                <Link href={`/products/${product.id}`}>
                                    EDIT
                                </Link>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
            <div className={styles.paginationContainer}>
                <nav aria-label="Pagination">
                    <ul className={styles.paginationList}>
                        {/* Pagination items */}
                        {currentPage > 1 && (
                            <li className={styles.pageItem}>
                                <button
                                    className={styles.pageLink}
                                    onClick={() =>
                                        handlePageChange(currentPage - 1)
                                    }
                                >
                                    Previous
                                </button>
                            </li>
                        )}
                        {Array.from(
                            { length: Math.min(2, currentPage - 1) },
                            (_, i) => currentPage - i - 1
                        ).map((page) => (
                            <li key={page} className={styles.pageItem}>
                                <button
                                    className={styles.pageLink}
                                    onClick={() => handlePageChange(page)}
                                >
                                    {page}
                                </button>
                            </li>
                        ))}
                        <li className={`${styles.pageItem} ${styles.active}`}>
                            <button className={styles.pageLink} disabled>
                                {currentPage}
                            </button>
                        </li>
                        {Array.from(
                            {
                                length: Math.min(2, totalPages - currentPage),
                            },
                            (_, i) => i + currentPage + 1
                        ).map((page) => (
                            <li key={page} className={styles.pageItem}>
                                <button
                                    className={styles.pageLink}
                                    onClick={() => handlePageChange(page)}
                                >
                                    {page}
                                </button>
                            </li>
                        ))}
                        {currentPage < totalPages && (
                            <li className={styles.pageItem}>
                                <button
                                    className={styles.pageLink}
                                    onClick={() =>
                                        handlePageChange(currentPage + 1)
                                    }
                                >
                                    Next
                                </button>
                            </li>
                        )}
                    </ul>
                </nav>
            </div>
        </div>
    );
};

export default TableComponent;

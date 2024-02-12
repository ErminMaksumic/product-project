import React, { ChangeEvent, useRef, useState } from "react";
import styles from "./FileUploader.module.scss";
import { Batch } from "@/lib/product";
import { useProductApi } from "@/app/context/Product/ProductContext";

interface FileUploaderProps {
    title: string;
    onFileUpload: (file: File) => Promise<Batch>;
}

const FileUploader: React.FC<FileUploaderProps> = ({ title, onFileUpload }) => {
    const { fetchBatchProgress } = useProductApi();
    const fileInputRef = useRef<HTMLInputElement>(null);
    const [selectedFile, setSelectedFile] = useState<File | null>(null);
    const [error, setError] = useState<string | null>(null);
    const [uploading, setUploading] = useState<boolean>(false);
    const [batchProgress, setBatchProgress] = useState<number>(0);

    const handleButtonClick = () => {
        if (fileInputRef.current) {
            fileInputRef.current.click();
        }
    };

    const handleFileChange = (e: ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files && e.target.files[0];
        if (file) {
            const allowedTypes = [
                "text/csv",
                "application/vnd.ms-excel",
                "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            ];
            if (!allowedTypes.includes(file.type)) {
                setError("Only CSV, XLS, and XLSX files are allowed.");
            } else {
                setSelectedFile(file);
                setError(null);
            }
        }
    };

    const handleUploadDataClick = async () => {
        if (selectedFile) {
            setUploading(true);
            try {
                const response = await onFileUpload(selectedFile);
                console.log("batch_id: " + response.batch_id);
                await pollProgress(response.batch_id);
            } catch (error) {
                console.error("Error uploading file:", error);
            }
        }
    };

    const pollProgress = async (batch_id: string) => {
        const interval = setInterval(async () => {
            const progress = await fetchBatchProgress(batch_id);
            setBatchProgress(progress);
            if (progress === 100) {
                clearInterval(interval);
                setUploading(false);
            }
        }, 3000);
    };

    return (
        <div className={styles.container}>
            <h4 className={styles.title}>{title}</h4>
            <div className={styles.fileUploader}>
                <div className={styles.fileInputContainer}>
                    <input
                        type="file"
                        name="mycsv"
                        ref={fileInputRef}
                        accept=".csv,.xls,.xlsx"
                        onChange={handleFileChange}
                        className={styles.fileInput}
                        hidden
                    />
                    <input
                        type="text"
                        value={selectedFile ? selectedFile.name : ""}
                        readOnly
                        className={styles.fileNameInput}
                    />
                    <button
                        onClick={handleButtonClick}
                        className={styles.uploadButton}
                    >
                        Choose File
                    </button>
                </div>
                {selectedFile && !uploading && (
                    <button
                        disabled={uploading}
                        onClick={handleUploadDataClick}
                        className={styles.uploadDataButton}
                    >
                        Upload Data
                    </button>
                )}
                {uploading && (
                    <div className={styles.progressBarContainer}>
                        <div
                            className={styles.progressBar}
                            style={{ width: `${batchProgress}%` }}
                        >
                            {batchProgress}%
                        </div>
                    </div>
                )}
                {error && <p className={styles.error}>{error}</p>}
            </div>
        </div>
    );
};

export default FileUploader;

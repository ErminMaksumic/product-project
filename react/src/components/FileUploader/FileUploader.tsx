import React, { ChangeEvent, useEffect, useRef, useState } from "react";
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
            const allowedTypes = ["text/csv"];
            if (!allowedTypes.includes(file.type)) {
                setError("Only CSV files are allowed.");
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
                setUploading(false);
                console.error("Error uploading file:", error);
            }
        }
    };

    //IZBRISI
   useEffect(() => {
    const fetchData = async () => {
        setUploading(true);
        await pollProgress('9b538511-f657-4a7b-b112-f3b056253f5c');
    };

    fetchData();
}, []);


    const pollProgress = async (batch_id: string) => {
        let previousProgress = 0;
        let consecutiveSameProgressCount = 0;
        const timeoutDuration = 40000;

        const interval = setInterval(async () => {
            const progress = await fetchBatchProgress(batch_id);
            setBatchProgress(progress);

            if (progress === 100) {
                clearInterval(interval);
                setUploading(false);
            } else if (progress === previousProgress) {
                consecutiveSameProgressCount += 3000;

                if (consecutiveSameProgressCount >= timeoutDuration) {
                    clearInterval(interval);
                    setUploading(false);
                    setSelectedFile(null);
                    setError("Upload failed please try again");
                }
            } else {
                previousProgress = progress;
                consecutiveSameProgressCount = 0;
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
                        accept=".csv"
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

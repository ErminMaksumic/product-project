import React, { ChangeEvent, useEffect, useRef, useState } from "react";
import styles from "./FileUploader.module.scss";

interface FileUploaderProps {
    title: string;
    onFileUpload: (file: File) => Promise<any>;
}

const FileUploader: React.FC<FileUploaderProps> = ({ title, onFileUpload }) => {
    const fileInputRef = useRef<HTMLInputElement>(null);
    const [selectedFile, setSelectedFile] = useState<File | null>(null);
    const [message, setMessage] = useState<{ message: string; color: string } | null>(
        null
    );

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
                setMessage({ message: "Only CSV files are allowed.", color: "red" });
                setSelectedFile(null);
            } else {
                setSelectedFile(file);
                setMessage(null);
            }
        }
    };

    const handleUploadDataClick = async () => {
        if (selectedFile) {
            try {
                setMessage({ message: "File is uploading please wait...", color: "#007bff" });
                await onFileUpload(selectedFile);
                setMessage({ message: "File uploaded successfully", color: "green" }); 
                setSelectedFile(null);
            } catch (error) {
                setMessage({ message: "File uploading failed", color: "red" });
                console.error("Error uploading file:", error);
            }
        }
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
                {selectedFile && !message?.message &&(
                    <button
                        onClick={handleUploadDataClick}
                        className={styles.uploadDataButton}
                    >
                        Upload Data
                    </button>
                )}

                {message && <p style={{ color: message.color }}>{message.message}</p>}
            </div>
        </div>
    );
};

export default FileUploader;

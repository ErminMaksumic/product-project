import React, { ChangeEvent, useRef, useState } from 'react';
import styles from './FileUploader.module.scss';

interface FileUploaderProps {
  title: string;
  onFileUpload: (file: File) => void;
}

const FileUploader: React.FC<FileUploaderProps> = ({ title, onFileUpload }) => {
  const fileInputRef = useRef<HTMLInputElement>(null);
  const [selectedFile, setSelectedFile] = useState<File | null>(null);
  const [error, setError] = useState<string | null>(null);

  const handleButtonClick = () => {
    if (fileInputRef.current) {
      fileInputRef.current.click();
    }
  };

  const handleFileChange = (e: ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files && e.target.files[0];
    if (file) {
      const allowedTypes = ['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
      if (!allowedTypes.includes(file.type)) {
        setError('Only CSV, XLS, and XLSX files are allowed.');
      } else {
        setSelectedFile(file);
        setError(null);
        onFileUpload(file);
      }
    }
  };

  const handleUploadDataClick = () => {
    // Handle uploading data
    console.log('Uploading data...');
  };

  return (
    <div className={styles.container}>
      <h4 className={styles.title}>{title}</h4>
       <div className={styles.fileUploader}>
      <div className={styles.fileInputContainer}>
        <input
          type="file"
          ref={fileInputRef}
          accept=".csv,.xls,.xlsx"
          onChange={handleFileChange}
          className={styles.fileInput}
          hidden
        />
        <input
          type="text"
          value={selectedFile ? selectedFile.name : ''}
          readOnly
          className={styles.fileNameInput}
        />
        <button onClick={handleButtonClick} className={styles.uploadButton}>Choose File</button>
      </div>
      {selectedFile && (
        <button onClick={handleUploadDataClick} className={styles.uploadDataButton}>Upload Data</button>
      )}
      {error && <p className={styles.error}>{error}</p>}
    </div>
    </div>
   
  );
};

export default FileUploader;

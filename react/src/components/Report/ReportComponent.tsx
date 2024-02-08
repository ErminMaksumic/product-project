import React, { useState } from "react";
import styles from "./ReportComponent.module.scss";
import { useProductApi } from "@/app/context/Product/ProductContext";

const ReportComponent: React.FC = () => {
    const {
        generateReportForExpensiveProducts,
        generateReportForOneProduct,
        generateReportForProductStatesGraph,
    } = useProductApi();

    const singleSelectOptions = [
        "Singular product (ID:1)",
        "Expensive products",
        "Product chart by status",
    ];
    const multiSelectOptions = [
        "PDF",
        "XLS",
        "XLSX",
        "DOCX",
        "PPTX",
        "CSV",
        "HTML",
        "RTF",
        "TXT",
        "XML",
        "ODT",
        "ODS",
    ];

    const [selectedSingleOption, setSelectedSingleOption] = useState<string>(
        singleSelectOptions[0]
    );
    const [selectedMultiOptions, setSelectedMultiOptions] = useState<string[]>(
        []
    );
    const [multiSelectDropdownOpen, setMultiSelectDropdownOpen] =
        useState<boolean>(false);

    const handleSingleSelectChange = (
        event: React.ChangeEvent<HTMLSelectElement>
    ) => {
        setSelectedSingleOption(event.target.value);
    };

    const toggleMultiSelectDropdown = () => {
        setMultiSelectDropdownOpen(!multiSelectDropdownOpen);
    };

    const handleMultiSelectItemClick = (option: string) => {
        if (selectedMultiOptions.includes(option)) {
            setSelectedMultiOptions((prevOptions) =>
                prevOptions.filter((item) => item !== option)
            );
        } else {
            setSelectedMultiOptions((prevOptions) => [...prevOptions, option]);
        }
    };

    const handleRemoveItem = (item: string) => {
        setSelectedMultiOptions((prevOptions) =>
            prevOptions.filter((option) => option !== item)
        );
    };

    const handleConfirmClick = async () => {
        const requestBody = {
            formats: selectedMultiOptions,
        };

        switch (selectedSingleOption) {
            case "Singular product (ID:1)":
                try {
                    await generateReportForOneProduct(1, requestBody);
                    console.log("Singular product: SUCCESSFUL");
                } catch (error) {
                    console.error(
                        "Error generating report for singular product:",
                        error
                    );
                }
                break;
            case "Expensive products":
                try {
                    await generateReportForExpensiveProducts(requestBody);
                    console.log("Expensive products: SUCCESSFUL");
                } catch (error) {
                    console.error(
                        "Error generating report for expensive products:",
                        error
                    );
                }
                break;
            case "Product chart by status":
                try {
                    await generateReportForProductStatesGraph(requestBody);
                    console.log("Product chart by status: SUCCESSFUL");
                } catch (error) {
                    console.error(
                        "Error generating product chart by status:",
                        error
                    );
                }
                break;
            default:
                console.error("Invalid option selected");
                return;
        }
    };

    return (
        <div className={styles.container}>
            <div className={styles.wrapper}>
                <div className={styles.containerWrapper}>
                    <label htmlFor="singleSelect" className={styles.label}>
                        Choose Report:
                    </label>
                    <select
                        id="singleSelect"
                        value={selectedSingleOption}
                        onChange={handleSingleSelectChange}
                        className={styles.dropdown}
                    >
                        {singleSelectOptions.map((option) => (
                            <option key={option} value={option}>
                                {option}
                            </option>
                        ))}
                    </select>
                </div>
                <div className={styles.containerWrapper}>
                    <label htmlFor="multiSelect" className={styles.label}>
                        Choose Format Types:
                    </label>
                    <div
                        className={styles.multiSelectDropdown}
                        onClick={toggleMultiSelectDropdown}
                    >
                        Select types
                        {multiSelectDropdownOpen && (
                            <div className={styles.dropdownContent}>
                                {multiSelectOptions.map((option) => (
                                    <div
                                        key={option}
                                        className={styles.dropdownItem}
                                        onClick={() =>
                                            handleMultiSelectItemClick(option)
                                        }
                                    >
                                        {option}
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
                <button
                    className={styles.confirmButton}
                    onClick={handleConfirmClick}
                >
                    Download Report
                </button>
            </div>
            <div className={styles.selectedItems}>
                {selectedMultiOptions.map((item) => (
                    <div key={item} className={styles.selectedItem}>
                        {item}
                        <button
                            className={styles.removeButton}
                            onClick={() => handleRemoveItem(item)}
                        >
                            X
                        </button>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default ReportComponent;

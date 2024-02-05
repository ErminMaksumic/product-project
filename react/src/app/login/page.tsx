"use client";
import React, { useEffect, useState } from "react";
import styles from "./page.module.scss";
import { useUserApi } from "@/app/context/User/UserContext";

const LoginPage: React.FC<any> = ({ props }) => {
    const { login } = useUserApi();
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");

    const handleLogin = async () => {
        const { success, error } = await login(email, password);
        if (success) {
            window.location.href = "/products";
        }
    };

    useEffect(() => {
        const bt = localStorage.getItem("bearerToken");
        if (bt) {
            window.location.href = "/products";
        }
    }, []);
    return (
        <div className={styles.loginContainer}>
            <h1>Login User</h1>
            <form className={styles.loginForm}>
                <div className={styles.inputContainer}>
                    <label className={styles.label}>Email:</label>
                    <input
                        className={styles.input}
                        type="email"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                    />
                </div>
                <div className={styles.inputContainer}>
                    <label className={styles.label}>Password:</label>
                    <input
                        className={styles.input}
                        type="password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                    />
                </div>
                <button
                    className={styles.button}
                    type="button"
                    onClick={handleLogin}
                >
                    Login
                </button>
            </form>
        </div>
    );
};

export default LoginPage;

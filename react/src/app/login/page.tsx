"use client";
import React, { useEffect, useState } from "react";
import styles from "./page.module.scss";
import { useUserApi } from "@/app/context/User/UserContext";
import qs from 'qs';

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

    const signInWithOauth2 = () => {
        const queryString = {
            client_id: '5',
            redirect_uri: 'http://localhost:3000/callback',
            response_type: 'code',
            scope: ['products']
        };
        window.location.href = `http://localhost:8000/oauth/authorize?${qs.stringify(queryString)}`;
    };

    useEffect(() => {
        const bt = localStorage.getItem("accessToken");
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
                <button
                    className={styles.button}
                    type="button"
                    onClick={signInWithOauth2}
                >
                    Login with Oauth2
                </button>
            </form>
        </div>
    );
};

export default LoginPage;

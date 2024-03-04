'use client'
import { useEffect } from "react";
import axios from "axios";
import { useSearchParams } from "next/navigation";
import { useRouter } from "next/navigation";

const CallbackPage = () => {
    const searchParams = useSearchParams();
    const router = useRouter();

    useEffect(() => {
        const redirect = async () => {
            if (searchParams.get("code")) {
                try {
                    const response = await axios.post(
                        "http://localhost:8000/oauth/token",
                        {
                            grant_type: "authorization_code",
                            client_id: "4",
                            client_secret:
                                "VmmEI32yvnyKsuukm5OO9ZRpSnVrehqdRScGvIK2",
                            code: searchParams.get("code"),
                            redirect_uri: "http://localhost:3000/callback",
                        },
                        {
                            headers: {
                                Accept: "application/json",
                                "Content-Type": "application/json",
                            },
                        }
                    );

                    localStorage.setItem("accessToken", response.data.access_token);
                    localStorage.setItem(
                        "refreshToken",
                        response.data.refresh_token
                    );

                    router.push("/");
                } catch (error) {
                    console.log(error);
                    //   this.$router.push('/');
                }
            }
        };

        redirect();
    }, [searchParams, router]);

    return <>Authorizing...</>;
};

export default CallbackPage;

"use client";
import React, { createContext, useContext, ReactNode } from "react";
import { getUser, login, logout } from "./api";

interface UserContextProps {
  getUser: () => Promise<User>;
  login: (email: string, password: string) => Promise<any>;
  logout: () => Promise<void>;
}

const UserContext = createContext<UserContextProps | undefined>(undefined);

export const UserProvider: React.FC<{ children: ReactNode }> = ({
  children,
}) => {
  return (
    <UserContext.Provider
      value={{
        getUser,
        login,
        logout,
      }}
    >
      {children}
    </UserContext.Provider>
  );
};

export const useUserApi = () => {
  const context = useContext(UserContext);
  if (!context) {
    throw new Error("useUserApi must be used within an UserProvider");
  }
  return context;
};

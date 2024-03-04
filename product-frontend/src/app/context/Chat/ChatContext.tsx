"use client";
import React, { createContext, useContext, ReactNode } from "react";
import { getMessages, sendMessage } from "./api";

interface ChatProps {
  getMessages: () => Promise<any>;
  sendMessage: (message:string) => Promise<any>;
}

const ChatContext = createContext<ChatProps | undefined>(undefined);

export const ChatProvider: React.FC<{ children: ReactNode }> = ({
  children,
}) => {
  return (
    <ChatContext.Provider
      value={{
        getMessages,
        sendMessage
      }}
    >
      {children}
    </ChatContext.Provider>
  );
};

export const useChatApi = () => {
  const context = useContext(ChatContext);
  if (!context) {
    throw new Error("useChatApi must be used within an ChatProvider");
  }
  return context;
};

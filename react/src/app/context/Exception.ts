import React, { createContext, useContext, useState, ReactNode } from "react";

// Define the types
interface ExceptionContextProps {
  exception: Error | null;
  handleException: (error: Error) => void;
  clearException: () => void;
}

const ExceptionContext = createContext<ExceptionContextProps | undefined>(
  undefined
);

const ExceptionProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
  const [exception, setException] = useState<Error | null>(null);

  const handleException = (error: Error) => {
    setException(error);
    // You can also log the error or perform additional actions here
  };

  const clearException = () => {
    setException(null);
  };

  return (
    <ExceptionContext.Provider value={{ exception, handleException, clearException }}>
      {children}
    </ExceptionContext.Provider>
  );
};

const useException = (): ExceptionContextProps => {
  const context = useContext(ExceptionContext);
  if (!context) {
    throw new Error("useException must be used within an ExceptionProvider");
  }
  return context;
};

export { ExceptionProvider, useException };

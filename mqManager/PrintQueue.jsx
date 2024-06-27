import { Card, Flex, Text, Button } from "@tremor/react";
import React, { useState, useEffect } from "react";
import QuiltTable from "./QuiltTable";

export default function PrintQueue({
    checkedInQuilts,
    setCheckedInQuilts,
    setCheckedInQuiltsCount,
    fetchCheckedInQuilts,
}) {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    const handlePrintQueue = async () => {
        const storedQuilts =
            JSON.parse(localStorage.getItem("printedQuilts")) || [];
        if (storedQuilts.length === 0) {
            console.error("No quilts in print queue");
            setError("No quilts in print queue");
            return;
        }
        if (storedQuilts.length > 5) {
            setError("Cannot print more than 5 quilts at a time");
            return;
        }

        setLoading(true);
        setError(null);

        const quiltIds = checkedInQuilts.map((quilt) => quilt.id);

        try {
            const requestOptions = {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    ids: quiltIds,
                }),
            };

            const response = await fetch("/api/printQueue", requestOptions);

            if (!response.ok) {
                throw new Error("Failed to generate PDF");
            }

            setLoading(false);
            const blob = await response.blob();
            const url = URL.createObjectURL(blob);

            const a = document.createElement("a");
            a.style.display = "none";
            a.href = url;
            a.download = "print_queue.pdf";
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);

            console.log("PDF downloaded successfully");

            localStorage.removeItem("printedQuilts");
            setCheckedInQuilts([]);
            setCheckedInQuiltsCount(0);

            fetchCheckedInQuilts();
        } catch (error) {
            console.error("Error during PDF generation", error);
            setError("Error during PDF generation");
        } finally {
            setLoading(false);
        }
    };

    const handleClearQueue = () => {
        localStorage.removeItem("printedQuilts");
        setCheckedInQuilts([]);
        setCheckedInQuiltsCount(0);
    };

    useEffect(() => {
        const storedQuilts =
            JSON.parse(localStorage.getItem("printedQuilts")) || [];
        setCheckedInQuilts(storedQuilts);
        setCheckedInQuiltsCount(storedQuilts.length);
    }, []);

    return (
        <Card>
            <Flex justifyContent="space-between" alignItems="center">
                <Text> Print Queue </Text>
                <button
                    type="button"
                    onClick={handlePrintQueue}
                    className="text-white text-sm bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg px-2 py-1.5 text-center ml-4"
                    disabled={loading}
                >
                    {loading ? "Loading..." : "Print Checked-In Quilts"}
                </button>
                <Button
                    onClick={handleClearQueue}
                    disabled={loading || checkedInQuilts.length === 0}
                    className="ml-4 text-white text-sm bg-gradient-to-r from-red-500 via-red-600 to-red-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg px-2 py-1.5 text-center"
                >
                    Clear Print Queue
                </Button>
            </Flex>
            {error && <Text className="text-red-500">{error}</Text>}
            <QuiltTable
                title={"Print Queue"}
                quilts={{ data: checkedInQuilts }}
            />
        </Card>
    );
}

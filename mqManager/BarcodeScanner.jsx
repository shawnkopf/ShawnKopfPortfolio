import React, { useState } from "react";
import { QrReader } from "react-qr-reader";

export default function BarcodeScanner({
    scanType,
    setStateFunction,
    oldState,
    setScannerOpen,
}) {
    const [scannedQuiltId, setScannedQuiltId] = useState(null);

    const constraints = {
        facingMode: { exact: "environment" },
    };

    const handleScan = (result) => {
        if (result) {
            setScannedQuiltId(result);
            if (scanType === "redirect") {
                window.location.href = "/" + result;
            } else {
                setStateFunction({ ...oldState, location: result });
                setScannerOpen(false);
            }
        }
    };

    const handleError = (error) => {
        console.error(error);
    };

    return (
        <>
            <QrReader
                onScan={handleScan}
                onError={handleError}
                style={{ width: "100%" }}
                constraints={constraints}
            />
            {scannedQuiltId && (
                <>
                    <QuiltInfoList quiltId={scannedQuiltId} />
                    <QuiltDetailsList quiltId={scannedQuiltId} />
                </>
            )}
        </>
    );
}

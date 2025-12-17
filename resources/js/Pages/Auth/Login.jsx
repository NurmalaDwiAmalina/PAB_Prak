// resources/js/Pages/Auth/Login.jsx
import React, { useState } from "react";

export default function Login() {
    const [email, setEmail] = useState("employee@example.com");
    const [password, setPassword] = useState("password");

    return (
        <div className="max-w-sm mx-auto mt-24 space-y-4">
            <h1 className="text-2xl font-bold">Sign in</h1>
            <form method="POST" action="/login" className="space-y-3">
                <input
                    name="_token"
                    type="hidden"
                    value={document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content")}
                />
                <input
                    className="w-full border p-2"
                    name="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                />
                <input
                    className="w-full border p-2"
                    name="password"
                    type="password"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                />
                <button className="px-3 py-2 rounded bg-black text-white">
                    Login
                </button>
            </form>
        </div>
    );
}

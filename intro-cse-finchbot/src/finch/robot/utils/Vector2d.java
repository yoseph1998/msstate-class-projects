package finch.robot.utils;

/**
 * A 2d vector class that also has the methods for doing some vector math.
 */
public class Vector2d {
    private double x, y;

    public Vector2d(double x, double y) {
        this.x = x;
        this.y = y;
    }

    /**
     * @param v The vector to add by.
     * @return The resultant vector of the two added.
     */
    public Vector2d add(Vector2d v) {
        return new Vector2d(x + v.getX(), y + v.getY());
    }

    /**
     * Subtracts vectors in the format: (this - v).
     *
     * @param v The vector to subtract by.
     * @return The resultant vector of the two subtracted.
     */
    public Vector2d subtract(Vector2d v) {
        return new Vector2d(x - v.getX(), y - v.getY());
    }

    /**
     * @param k The constant to multiply with this vector.
     * @return The resultant vector from multiplying this vector with k.
     */
    public Vector2d multiply(double k) {
        return new Vector2d(x * k, y * k);
    }

    /**
     * @return The unit vector.
     */
    public Vector2d getUnitVector() {
        return multiply(1.0 / getMagnitude());
    }

    /**
     * @return The angle of the vector in degrees in a unit circle.
     */
    public double getAngle() {
        if (x != 0)
            return Math.toDegrees(Math.atan(y / x));
        return 0;
    }

    /**
     * @return The magnitude of the vector.
     */
    public double getMagnitude() {
        return Math.sqrt(Math.pow(x, 2) + Math.pow(y, 2));
    }

    /**
     * @return The X value of the vector.
     */
    public double getX() {
        return x;
    }

    /**
     * @return The Y value of the vector.
     */
    public double getY() {
        return y;
    }
}
